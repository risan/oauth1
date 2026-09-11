<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * The AuthorizationHeaderInterface instance.
     */
    protected AuthorizationHeaderInterface $authorizationHeader;

    /**
     * The UriParserInterface instance.
     */
    protected UriParserInterface $uriParser;

    /**
     * Create the new instance of RequestFactory class.
     */
    public function __construct(AuthorizationHeaderInterface $authorizationHeader, UriParserInterface $uriParser)
    {
        $this->authorizationHeader = $authorizationHeader;
        $this->uriParser = $uriParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationHeader(): AuthorizationHeaderInterface
    {
        return $this->authorizationHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->authorizationHeader->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriParser(): UriParserInterface
    {
        return $this->uriParser;
    }

    /**
     * {@inheritdoc}
     */
    public function createForTemporaryCredentials(): RequestInterface
    {
        $config = $this->getConfig();

        return $this->create($config->getTemporaryCredentialsMethod(), (string) $config->getTemporaryCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTemporaryCredentials(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials): UriInterface
    {
        return $this->uriParser->appendQueryParameters(
            $this->getConfig()->getAuthorizationUri(),
            ['oauth_token' => $temporaryCredentials->getIdentifier()]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createForTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): RequestInterface
    {
        $config = $this->getConfig();
        $method = $config->getTokenCredentialsMethod();
        $parameterOption = $method === 'GET' ? 'query' : 'form_params';

        return $this->create($method, (string) $config->getTokenCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTokenCredentials($temporaryCredentials, $verificationCode),
            ],
            $parameterOption => [
                'oauth_verifier' => $verificationCode,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createForProtectedResource(TokenCredentials $tokenCredentials, string $method, UriInterface|string $uri, array $options = []): RequestInterface
    {
        $resolvedUri = $this->getConfig()->buildUri($uri);
        [$resolvedUri, $options] = $this->normalizeOptions($resolvedUri, $options);
        $headers = $options['headers'] ?? [];

        $options['headers'] = $this->withHeader($headers, 'Authorization', $this->authorizationHeader->forProtectedResource(
            $tokenCredentials,
            $method,
            $resolvedUri,
            $options,
        ));

        return $this->create($method, (string) $resolvedUri, $options);
    }

    /**
     * Create a new instance of Request class.
     *
     * @param  array  $options  [description]
     */
    public function create(string $method, string $uri, array $options = []): RequestInterface
    {
        return new Request($method, $uri, $options);
    }

    /** @return array{0: UriInterface, 1: array<array-key, mixed>} */
    private function normalizeOptions(UriInterface $uri, array $options): array
    {
        $headers = $options['headers'] ?? [];
        if (! is_array($headers)) {
            throw new InvalidArgumentException('The OAuth request headers option must be an array.');
        }

        foreach ($headers as $name => $_value) {
            if (strcasecmp((string) $name, 'Authorization') === 0) {
                throw new InvalidArgumentException('The Authorization header is managed by the OAuth client and cannot be overridden.');
            }
        }

        if (isset($options['query']) && $options['query'] !== [] && $options['query'] !== '') {
            $query = is_string($options['query'])
                ? ParameterList::fromQueryString($options['query'])
                : (is_array($options['query'])
                    ? ParameterList::fromArray($options['query'])
                    : throw new InvalidArgumentException('The query OAuth request option must be an array or query string.'));
            $uri = $uri->withQuery(
                ParameterList::fromQueryString($uri->getQuery())->merge($query)->toQueryString(),
            );
            unset($options['query']);
        }

        if (isset($options['form_params'])) {
            if (! is_array($options['form_params'])) {
                throw new InvalidArgumentException('The form_params OAuth request option must be an array.');
            }

            $form = ParameterList::fromArray($options['form_params']);
            $contentType = $this->contentType($headers);
            if ($contentType !== null && $contentType !== 'application/x-www-form-urlencoded') {
                throw new InvalidArgumentException('The form_params option requires an application/x-www-form-urlencoded Content-Type.');
            }

            if (array_is_list($options['form_params']) && $options['form_params'] !== []) {
                unset($options['form_params']);
                $options['body'] = $form->toQueryString();
                $headers = $this->withHeader($headers, 'Content-Type', 'application/x-www-form-urlencoded');
            }
        }

        if ($headers !== [] || array_key_exists('headers', $options)) {
            $options['headers'] = $headers;
        }

        return [$uri, $options];
    }

    private function contentType(array $headers): ?string
    {
        foreach ($headers as $name => $values) {
            if (strcasecmp((string) $name, 'Content-Type') !== 0) {
                continue;
            }

            $value = (array) $values;

            return strtolower(trim(explode(';', (string) ($value[0] ?? ''), 2)[0]));
        }

        return null;
    }

    private function withHeader(array $headers, string $name, string $value): array
    {
        foreach (array_keys($headers) as $existing) {
            if (strcasecmp((string) $existing, $name) === 0) {
                unset($headers[$existing]);
            }
        }

        $headers[$name] = $value;

        return $headers;
    }
}
