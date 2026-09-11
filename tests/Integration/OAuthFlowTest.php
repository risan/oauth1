<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface as PsrRequest;
use Risan\OAuth1\Config\ConfigFactory;
use Risan\OAuth1\Credentials\CredentialsFactory;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\HttpClient;
use Risan\OAuth1\OAuth1;
use Risan\OAuth1\Request\AuthorizationHeader;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Request\ProtocolParameter;
use Risan\OAuth1\Request\RequestFactory;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Signature\HmacSha1Signer;

function integrationClient(array &$history): OAuth1
{
    $mock = new MockHandler([
        new Response(200, [], 'oauth_token=request-token&oauth_token_secret=request-secret&oauth_callback_confirmed=true'),
        new Response(200, [], 'oauth_token=access-token&oauth_token_secret=access-secret'),
        new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
    ]);
    $stack = HandlerStack::create($mock);
    $stack->push(Middleware::history($history));

    $config = (new ConfigFactory)->createFromArray([
        'client_credentials_identifier' => 'consumer-key',
        'client_credentials_secret' => 'consumer-secret',
        'temporary_credentials_uri' => 'https://service.test/oauth/request_token',
        'authorization_uri' => 'https://service.test/oauth/authorize',
        'token_credentials_uri' => 'https://service.test/oauth/access_token',
        'callback_uri' => 'https://client.test/callback',
        'base_uri' => 'https://service.test/api/',
    ]);
    $nonce = new class implements NonceGeneratorInterface
    {
        public function generate(int $length = 32): string
        {
            return 'fixed-nonce';
        }
    };
    $protocol = new class($config, new HmacSha1Signer, $nonce) extends ProtocolParameter
    {
        public function getCurrentTimestamp(): int
        {
            return 1_700_000_000;
        }
    };
    $requests = new RequestFactory(new AuthorizationHeader($protocol), new UriParser);

    return new OAuth1(new HttpClient(new Client(['handler' => $stack])), $requests, new CredentialsFactory);
}

/** @return array<string, string> */
function oauthHeader(PsrRequest $request): array
{
    $header = preg_replace('/^OAuth\s+/', '', $request->getHeaderLine('Authorization'));
    $parameters = [];
    foreach (preg_split('/,\s*/', (string) $header) as $field) {
        if (preg_match('/^([^=]+)="(.*)"$/', $field, $matches) === 1) {
            $parameters[rawurldecode($matches[1])] = rawurldecode($matches[2]);
        }
    }

    return $parameters;
}

function independentlyVerify(PsrRequest $request, string $consumerSecret, string $tokenSecret = ''): void
{
    $oauth = oauthHeader($request);
    $actual = $oauth['oauth_signature'];
    unset($oauth['oauth_signature']);
    $pairs = [];
    foreach ($oauth as $name => $value) {
        $pairs[] = [$name, $value];
    }
    foreach ([$request->getUri()->getQuery(), str_starts_with($request->getHeaderLine('Content-Type'), 'application/x-www-form-urlencoded') ? (string) $request->getBody() : ''] as $query) {
        if ($query === '') {
            continue;
        }
        foreach (explode('&', $query) as $field) {
            [$name, $value] = array_pad(explode('=', $field, 2), 2, '');
            $pairs[] = [urldecode($name), urldecode($value)];
        }
    }
    $pairs = array_map(static fn (array $pair): array => [rawurlencode($pair[0]), rawurlencode($pair[1])], $pairs);
    usort($pairs, static fn (array $a, array $b): int => $a[0] <=> $b[0] ?: $a[1] <=> $b[1]);
    $normalized = implode('&', array_map(static fn (array $pair): string => $pair[0].'='.$pair[1], $pairs));
    $uri = $request->getUri();
    $baseUri = strtolower($uri->getScheme()).'://'.strtolower($uri->getHost()).($uri->getPath() === '' ? '/' : $uri->getPath());
    $base = strtoupper($request->getMethod()).'&'.rawurlencode($baseUri).'&'.rawurlencode($normalized);
    $expected = base64_encode(hash_hmac('sha1', $base, rawurlencode($consumerSecret).'&'.rawurlencode($tokenSecret), true));

    expect($actual)->toBe($expected);
}

test('completes the OAuth 1.0a flow and signs the exact HTTP requests', function (): void {
    $history = [];
    $oauth = integrationClient($history);

    $temporary = $oauth->requestTemporaryCredentials();
    expect($temporary->getIdentifier())->toBe('request-token')
        ->and($oauth->buildAuthorizationUri($temporary))->toBe('https://service.test/oauth/authorize?oauth_token=request-token');

    $token = $oauth->requestTokenCredentials($temporary, 'request-token', 'verifier-code');
    $oauth->setTokenCredentials($token);
    $response = $oauth->post('items?fixed=one&fixed=two', [
        'query' => [['tag', 'one'], ['tag', 'two'], ['empty', '']],
        'form_params' => [['item', 'first'], ['item', 'second'], ['disabled', false]],
    ]);

    expect((string) $response->getBody())->toBe('{"ok":true}')
        ->and($history)->toHaveCount(3);

    $requestToken = $history[0]['request'];
    expect(oauthHeader($requestToken)['oauth_callback'])->toBe('https://client.test/callback');
    independentlyVerify($requestToken, 'consumer-secret');

    $accessToken = $history[1]['request'];
    expect((string) $accessToken->getBody())->toBe('oauth_verifier=verifier-code');
    independentlyVerify($accessToken, 'consumer-secret', 'request-secret');

    $protected = $history[2]['request'];
    expect($protected->getUri()->getQuery())->toBe('fixed=one&fixed=two&tag=one&tag=two&empty=')
        ->and((string) $protected->getBody())->toBe('item=first&item=second&disabled=0')
        ->and($protected->getHeaderLine('Content-Type'))->toStartWith('application/x-www-form-urlencoded');
    independentlyVerify($protected, 'consumer-secret', 'access-secret');
});

test('rejects caller Authorization headers before dispatch', function (): void {
    $history = [];
    $oauth = integrationClient($history);
    $oauth->setTokenCredentials(new TokenCredentials('access-token', 'access-secret'));

    $oauth->get('items', ['headers' => ['authorization' => 'Bearer attacker']]);
})->throws(InvalidArgumentException::class, 'cannot be overridden');

test('rejects a content type that conflicts with form parameters', function (): void {
    $history = [];
    $oauth = integrationClient($history);
    $oauth->setTokenCredentials(new TokenCredentials('access-token', 'access-secret'));

    $oauth->post('items', [
        'headers' => ['Content-Type' => 'application/json'],
        'form_params' => ['name' => 'not-json'],
    ]);
})->throws(InvalidArgumentException::class, 'form_params option requires');
