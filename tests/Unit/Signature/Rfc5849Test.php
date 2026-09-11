<?php

declare(strict_types=1);

use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Signature\BaseStringBuilder;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\RsaSha1Signer;

test('builds the RFC 5849 signature base string and HMAC-SHA1 signature', function (): void {
    $parameters = [
        ['b5', '=%3D'],
        ['a3', 'a'],
        ['c@', ''],
        ['a2', 'r b'],
        ['oauth_consumer_key', '9djdj82h48djs9d2'],
        ['oauth_token', 'kkk9d7dh3k39sjv7'],
        ['oauth_signature_method', 'HMAC-SHA1'],
        ['oauth_timestamp', '137131201'],
        ['oauth_nonce', '7d8f3e4a'],
        ['c2', ''],
        ['a3', '2 q'],
    ];
    $builder = new BaseStringBuilder(new UriParser);

    $baseString = $builder->build('POST', 'http://example.com/request', $parameters);

    expect($baseString)->toBe(
        'POST&http%3A%2F%2Fexample.com%2Frequest&a2%3Dr%2520b%26a3%3D2%2520q%26a3%3Da%26b5%3D%253D%25253D%26c%2540%3D%26c2%3D%26oauth_consumer_key%3D9djdj82h48djs9d2%26oauth_nonce%3D7d8f3e4a%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D137131201%26oauth_token%3Dkkk9d7dh3k39sjv7'
    );

    $signer = new HmacSha1Signer;
    $signer->setClientCredentials(new ClientCredentials('ignored', 'j49sk3j29djd'));
    $signer->setServerIssuedCredentials(new TokenCredentials('ignored', 'dh893hdasih9'));
    expect($signer->sign('http://example.com/request', $parameters, 'POST'))->toBe('r6/TJjbCOr97/+UU0NsvSne7s5g=');
});

test('preserves repeated query names and form collisions and sorts by encoded value', function (): void {
    $builder = new BaseStringBuilder(new UriParser);

    expect($builder->buildParametersComponent([
        ['a', 'z'],
        ['a', ''],
        ['a', '2 q'],
        ['a', 'a'],
        ['plus', '+'],
        ['space', ' '],
    ]))->toBe('a=&a=2%20q&a=a&a=z&plus=%2B&space=%20');

    expect($builder->build('GET', 'HTTPS://EXAMPLE.COM:443?x=1&x=2', [['x', '3']]))
        ->toContain('https%3A%2F%2Fexample.com%2F')
        ->toEndWith('x%3D1%26x%3D2%26x%3D3');
});

test('excludes oauth_signature from normalization', function (): void {
    $builder = new BaseStringBuilder(new UriParser);

    expect($builder->buildParametersComponent([
        ['oauth_nonce', 'nonce'],
        ['oauth_signature', 'must not be signed'],
    ]))->toBe('oauth_nonce=nonce');
});

test('creates an RSA-SHA1 signature that verifies with the public key', function (): void {
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    expect($key)->not->toBeFalse();
    openssl_pkey_export($key, $privatePem);
    $publicPem = openssl_pkey_get_details($key)['key'];

    $signer = new RsaSha1Signer($privatePem);
    $parameters = [['oauth_nonce', 'nonce']];
    $signature = base64_decode($signer->sign('https://example.com/resource', $parameters, 'GET'), true);

    expect($signer->getMethod())->toBe('RSA-SHA1')
        ->and($signer->isKeyBased())->toBeFalse()
        ->and(openssl_verify($signer->buildBaseString('https://example.com/resource', $parameters, 'GET'), $signature, $publicPem, OPENSSL_ALGO_SHA1))->toBe(1);
});

test('rejects an invalid RSA private key', function (): void {
    new RsaSha1Signer('not a key');
})->throws(InvalidArgumentException::class);
