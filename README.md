# OAuth 1.0a Client for PHP

[![CI](https://github.com/risan/oauth1/actions/workflows/ci.yml/badge.svg)](https://github.com/risan/oauth1/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/risan/oauth1/v/stable?format=flat-square)](https://packagist.org/packages/risan/oauth1)
[![Total Downloads](https://img.shields.io/packagist/dt/risan/oauth1.svg?style=flat-square)](https://packagist.org/packages/risan/oauth1)
[![License](https://img.shields.io/packagist/l/risan/oauth1.svg?style=flat-square)](LICENSE.md)

A strict PHP 8.5 client for the three-legged OAuth 1.0a flow defined by [RFC 5849](https://www.rfc-editor.org/rfc/rfc5849). It obtains temporary credentials, redirects the resource owner for approval, exchanges the verifier for token credentials, and signs protected-resource requests.

The client sends OAuth protocol parameters in the `Authorization` header and supports `HMAC-SHA1`, `RSA-SHA1`, and `PLAINTEXT`. HMAC-SHA1 is the default because it remains the most widely supported OAuth 1 signature method.

## Requirements

- PHP `^8.5`
- `ext-hash`
- `ext-openssl`
- Composer 2

Older PHP versions are not supported.

## Installation

```bash
composer require risan/oauth1
```

## OAuth 1.0a in five steps

The full flow uses three credential pairs:

- **Client credentials** identify your application: consumer key and consumer secret.
- **Temporary credentials** identify an authorization attempt: request token and request-token secret.
- **Token credentials** represent the approved access grant: access token and access-token secret.

The complete web flow is:

1. The client signs a request to the temporary-credentials endpoint with its client credentials and callback URI.
2. The server returns a temporary token, temporary secret, and `oauth_callback_confirmed=true`.
3. The client redirects the resource owner to the authorization endpoint with the temporary token.
4. The server redirects back with the same `oauth_token` and an `oauth_verifier`; the client exchanges both for token credentials.
5. The client signs each protected-resource request with its client credentials and token credentials.

Keep the temporary credentials in the same user session that started the flow. The library checks that the callback's `oauth_token` matches the stored temporary token before performing the exchange.

## Configuration

Create a client with the credentials and three OAuth endpoint URIs supplied by the server:

```php
<?php

declare(strict_types=1);

use Risan\OAuth1\OAuth1Factory;

require __DIR__.'/vendor/autoload.php';

$oauth = OAuth1Factory::create([
    'client_credentials_identifier' => $_ENV['OAUTH_CONSUMER_KEY'],
    'client_credentials_secret' => $_ENV['OAUTH_CONSUMER_SECRET'],
    'temporary_credentials_uri' => 'https://service.example/oauth/request_token',
    'authorization_uri' => 'https://service.example/oauth/authorize',
    'token_credentials_uri' => 'https://service.example/oauth/access_token',
    'callback_uri' => 'https://client.example/oauth/callback',
    'base_uri' => 'https://service.example/api/',
]);
```

| Key | Required | Meaning |
| --- | --- | --- |
| `client_credentials_identifier` | yes | Consumer key / API key |
| `client_credentials_secret` | yes | Consumer secret / API secret |
| `temporary_credentials_uri` | yes | Request-token endpoint |
| `authorization_uri` | yes | Resource-owner approval endpoint |
| `token_credentials_uri` | yes | Access-token endpoint |
| `callback_uri` | yes | Absolute callback URI, or the case-sensitive value `oob` for an out-of-band flow |
| `base_uri` | no | Base used to resolve relative API URIs |
| `temporary_credentials_method` | no | HTTP method for the request-token endpoint; defaults to `POST` |
| `token_credentials_method` | no | HTTP method for the access-token endpoint; defaults to `POST`; `GET` puts the verifier in the query |

Absolute endpoint URIs work without `base_uri`. Relative endpoint and resource URIs are resolved against it.

RFC 5849 requires `oauth_callback` when obtaining temporary credentials, even when the provider already knows the callback; use `oob` when the client cannot receive a callback.

## Start the authorization flow

Obtain temporary credentials and store their scalar values. Avoid serializing PHP objects into cookies or sessions.

```php
<?php

declare(strict_types=1);

session_start();

$temporary = $oauth->requestTemporaryCredentials();

$_SESSION['oauth1_temporary'] = [
    'identifier' => $temporary->getIdentifier(),
    'secret' => $temporary->getSecret(),
];

header('Location: '.$oauth->buildAuthorizationUri($temporary), true, 302);
exit;
```

`requestTemporaryCredentials()` rejects responses that omit the token, token secret, or `oauth_callback_confirmed=true`.

## Handle the callback

Rebuild the temporary credentials from the server-side session, then exchange the verifier. Validate that both callback parameters exist before calling the library.

```php
<?php

declare(strict_types=1);

use Risan\OAuth1\Credentials\TemporaryCredentials;

session_start();

if (!isset($_GET['oauth_token'], $_GET['oauth_verifier'], $_SESSION['oauth1_temporary'])) {
    throw new RuntimeException('The OAuth callback is incomplete.');
}

$stored = $_SESSION['oauth1_temporary'];
unset($_SESSION['oauth1_temporary']);

$temporary = new TemporaryCredentials($stored['identifier'], $stored['secret']);
$token = $oauth->requestTokenCredentials(
    $temporary,
    (string) $_GET['oauth_token'],
    (string) $_GET['oauth_verifier'],
);

$_SESSION['oauth1_token'] = [
    'identifier' => $token->getIdentifier(),
    'secret' => $token->getSecret(),
];
```

Store long-lived token credentials encrypted at rest when the provider keeps them valid beyond the session.

## Call protected resources

Restore the token credentials, set them on the client, and use `request()` or an HTTP-method shortcut:

```php
use Risan\OAuth1\Credentials\TokenCredentials;

$stored = $_SESSION['oauth1_token'];
$oauth->setTokenCredentials(new TokenCredentials($stored['identifier'], $stored['secret']));

$response = $oauth->get('account', [
    'query' => ['include' => 'profile'],
]);

$data = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
```

The return value implements `Psr\Http\Message\ResponseInterface`. The available shortcuts are `get()`, `post()`, `put()`, `patch()`, and `delete()`.

Request options are passed to Guzzle. The library owns the `Authorization` header and rejects attempts to replace it.

### Parameters and the signature

OAuth 1 signatures depend on the exact decoded parameter name/value pairs sent in the request. This library collects:

- OAuth protocol parameters except `oauth_signature`
- every parameter already present in the request URI query
- the Guzzle `query` option
- `form_params`, or a raw form body whose media type is `application/x-www-form-urlencoded`

JSON, multipart, and other entity bodies are not part of the RFC 5849 signature base string. Use HTTPS because OAuth 1 signatures do not cover most headers or non-form request bodies.

Associative arrays are convenient when parameter names are unique:

```php
$response = $oauth->post('items', [
    'form_params' => [
        'name' => 'Example',
        'enabled' => '1',
    ],
]);
```

Use an ordered list of `[name, value]` pairs when a query or form repeats a name. The library preserves these pairs both in the signature and on the wire:

```php
$response = $oauth->get('search', [
    'query' => [
        ['tag', 'php'],
        ['tag', 'oauth'],
        ['empty', ''],
    ],
]);
```

Nested arrays and `null` values are rejected because their wire encoding is ambiguous or omitted by Guzzle and cannot be normalized reliably under RFC 5849. Use an empty string for an empty value, encode structured data yourself as a scalar, or use JSON when the API accepts it.

## Signature methods

### HMAC-SHA1

HMAC-SHA1 is the default:

```php
$oauth = OAuth1Factory::create($config);
```

The signing key is the percent-encoded client secret, an ampersand, and the percent-encoded temporary or token secret.

### RSA-SHA1

RSA-SHA1 signs the RFC 5849 base string with an RSA private key. The client shared secret is not used by this method, but the configuration key must still be present and may be an empty string.

```php
use Risan\OAuth1\Signature\RsaSha1Signer;

$privateKey = file_get_contents('/secure/path/oauth-private-key.pem');
$oauth = OAuth1Factory::create($config, new RsaSha1Signer($privateKey, $_ENV['KEY_PASSPHRASE']));
```

### PLAINTEXT

```php
use Risan\OAuth1\Signature\PlainTextSigner;

$oauth = OAuth1Factory::create($config, new PlainTextSigner());
```

Use only a method documented by the OAuth server. These OAuth 1 methods use legacy SHA-1-era protocol primitives, so transport security remains required.

## Built-in provider configurations

The built-in providers supply endpoint URIs and HMAC-SHA1. Pass your credentials and callback URI:

```php
use Risan\OAuth1\ProviderFactory;

$oauth = ProviderFactory::tumblr([
    'client_credentials_identifier' => $_ENV['TUMBLR_CONSUMER_KEY'],
    'client_credentials_secret' => $_ENV['TUMBLR_CONSUMER_SECRET'],
    'callback_uri' => 'https://client.example/oauth/callback',
]);
```

Available factory methods are:

- `ProviderFactory::trello()` for [Trello OAuth 1](https://developer.atlassian.com/cloud/trello/guides/rest-api/authorization/)
- `ProviderFactory::tumblr()` for [Tumblr OAuth 1](https://github.com/tumblr/docs/blob/master/api.md); its access-token exchange uses `GET`
- `ProviderFactory::twitter()` for [X OAuth 1.0a](https://docs.x.com/fundamentals/authentication/oauth-1-0a/overview) using the current `api.x.com` endpoints

Provider products and endpoint policies can change independently of this package. Confirm API access, scopes, callback rules, and supported signature methods in the provider's current documentation. The former Upwork preset was removed because Upwork now documents OAuth 2.0 for its current API.

## Exceptions

- Invalid configuration or conflicting request options throw `InvalidArgumentException` or `TypeError`.
- Missing or malformed OAuth credential responses throw `Risan\OAuth1\Credentials\CredentialsException`.
- HTTP and transport failures use Guzzle's exception types.
- RSA key loading and signing failures throw `InvalidArgumentException` or `RuntimeException`.

## Development

The project uses Pest 5 as the test runner, PHPUnit 13 configuration, Laravel Pint, PHPStan level 8, and GitHub Actions on PHP 8.5.

```bash
composer validate --strict
composer install
composer test
composer format
composer analyse
```

The integration suite runs the complete OAuth flow against Guzzle's HTTP mock handler and independently recomputes each captured request signature. It does not contact third-party providers or require live credentials.

A reproducible PHP 8.5 CLI image is also provided:

```bash
docker build -t oauth1-php85 -f docker/php85/Dockerfile .
docker run --rm -v "$PWD":/app -w /app oauth1-php85 composer test
```

## Releasing through Packagist

`risan/oauth1` is already registered on Packagist. Packagist reads versions from Git tags, so `composer.json` intentionally has no `version` field.

For a release:

1. Merge a commit that passes CI.
2. Create and push a semantic version tag such as `v3.0.0`.
3. Packagist discovers the tag automatically when its GitHub hook is connected. Without a hook, use the maintainer page's manual update action or wait for the periodic crawl.
4. Verify that the new immutable tag and commit SHA appear on Packagist before announcing the release.

Pushing an ordinary branch updates its development version; it does not create a stable release. Do not move a published stable tag to another commit.

## License

MIT © [Risan Bagja Pradana](https://risan.io)
