# OAuth 1.0 Client Library for PHP

[![Latest Stable Version](https://poser.pugx.org/risan/oauth1/v/stable?format=flat-square)](https://packagist.org/packages/risan/oauth1)
[![Build Status](https://img.shields.io/travis/risan/oauth1.svg?style=flat-square)](https://travis-ci.org/risan/oauth1)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/risan/oauth1.svg?style=flat-square)](https://scrutinizer-ci.com/g/risan/oauth1/)
[![Code Quality](https://img.shields.io/scrutinizer/g/risan/oauth1.svg?style=flat-square)](https://scrutinizer-ci.com/g/risan/oauth1/)
[![StyleCI](https://styleci.io/repos/48460990/shield)](https://styleci.io/repos/48460990)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/258a9ce7-94cf-4a9d-a8ae-1add8fa5b8be.svg?style=flat-square)](https://insight.sensiolabs.com/projects/258a9ce7-94cf-4a9d-a8ae-1add8fa5b8be)
[![License](https://img.shields.io/packagist/l/risan/oauth1.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/risan/oauth1.svg?style=flat-square)](https://packagist.org/packages/risan/oauth1)

Simple, fluent and extensible OAuth 1.0 client library for PHP.

## Table of Contents

* [Installation](#installation)
* [Quick Start Guide](#quick-start-guide)
* [Configuration](#configuration)
* [Signature](#signature)
* [OAuth 1.0 Flow](#oauth-10-flow)
    * [Step 1: Obtaining Temporary Credentials](#step-1-obtaining-temporary-credentials)
    * [Step 2: Generate and Redirect User to Authorization URI](#step-2-generate-and-redirect-user-to-authorization-uri)
    * [Step 3: Obtaining Token Credentials](#step-3-obtaining-token-credentials)
    * [Step 4: Accessing the Protected Resource](#step-4-accessing-the-protected-resource)
* [Making HTTP Request](#making-http-request)
* [Working with the Response](#working-with-the-response)
* [Built-In Providers](#built-in-providers)
    * [Trello](#trello)
    * [Tumblr](#tumblr)
    * [Twitter](#twitter)
    * [Upwork](#upwork)

## Installation

The recommended way to install this package is through [Composer](https://getcomposer.org). Run the following command in your terminal to install this package:

```bash
composer require risan/oauth1
```

## Quick Start Guide

This package is flexible. You can use it to interact with any providers that implement OAuth 1.0 protocol, like Twitter.

Here's a quick example of how to use this package to interact with Twitter API: fetching the authorized user's tweets.

```php
<?php

// Includes the Composer autoload file.
require 'vendor/autoload.php';

// Start the session.
session_start();

// Create an instance of Risan\OAuth1\OAuth1 class.
$oauth1 = Risan\OAuth1\OAuth1Factory::create([
    'client_credentials_identifier' => 'YOUR_TWITTER_API_KEY',
    'client_credentials_secret' => 'YOUR_TWITTER_API_SECRET',
    'temporary_credentials_uri' => 'https://api.twitter.com/oauth/request_token',
    'authorization_uri' => 'https://api.twitter.com/oauth/authorize',
    'token_credentials_uri' => 'https://api.twitter.com/oauth/access_token',
    'callback_uri' => 'YOUR_CALLBACK_URI',
]);

if (isset($_SESSION['token_credentials'])) {
    // Get back the previosuly obtain token credentials (step 3).
    $tokenCredentials = unserialize($_SESSION['token_credentials']);
    $oauth1->setTokenCredentials($tokenCredentials);

    // STEP 4: Retrieve the user's tweets.
    // It will return the Psr\Http\Message\ResponseInterface instance.
    $response = $oauth1->request('GET', 'https://api.twitter.com/1.1/statuses/user_timeline.json');

    // Convert the response to array and display it.
    var_dump(json_decode($response->getBody()->getContents(), true));
} elseif (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
    // Get back the previosuly generated temporary credentials (step 1).
    $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);
    unset($_SESSION['temporary_credentials']);

    // STEP 3: Obtain the token credentials (also known as access token).
    $tokenCredentials = $oauth1->requestTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

    // Store the token credentials in session for later use.
    $_SESSION['token_credentials'] = serialize($tokenCredentials);

    // this basically just redirecting to the current page so that the query string is removed.
    header('Location: ' . (string) $oauth1->getConfig()->getCallbackUri());
    exit();
} else {
    // STEP 1: Obtain a temporary credentials (also known as the request token)
    $temporaryCredentials = $oauth1->requestTemporaryCredentials();

    // Store the temporary credentials in session so we can use it on step 3.
    $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);

    // STEP 2: Generate and redirect user to authorization URI.
    $authorizationUri = $oauth1->buildAuthorizationUri($temporaryCredentials);
    header("Location: {$authorizationUri}");
    exit();
}
```

## Configuration

You can use the static `create` method on `Risan\OAuth1\OAuth1Factory` class to easily create an instance of `Risan\OAuth1\Auth1` class. It requires you to pass a configuration array with the following keys:

* `client_credentials_identifier`: The client credentials identifier, also known as a consumer key or API key.
* `client_credentials_secret`: The client credentials secret, also known as a consumer secret or API secret.
* `temporary_credentials_uri`: The URI for obtaining the temporary credentials (also known as request token).
* `authorization_uri`: The URI for authorizing user.
* `token_credentials_uri`: The URI for obtaining the token credentials (also known as access token).

There are also two optional configuration that you can pass:
* `callback_uri`: The URI where the user will be redirected to after successfull authorization.
* `base_uri`: The base URI that will be used to build an absolute URI if you pass a relative URI to configuration array or when sending a request to the protected resource.

## Signature

Each HTTP request must include a signature so that the provider can verify the authenticity of that request. This signing process is handled by the signer instance that implements the `Risan\OAuth1\Signature\SignerInterface` interface. This package includes two signer classes that you can use:

* `Risan\OAuth1\Signature\HmacSha1Signer`: for signing request with HMAC-SHA1 method.
* `Risan\OAuth1\Signature\PlainTextSigner`: for signing request with PLAIN TEXT method.

You can pass this signer instance as the second argument to the `create` static method on `Risan\OAuth1\OAuth1Factory` class:

```php
$plainTextSigner = new Risan\OAuth1\Signature\PlainTextSigner();

$oauth1 = Risan\OAuth1\OAuth1Factory::create($config, $plainTextSigner);
```

If you do not pass any signer instance to the `create` method, the default HMAC-SHA1 signer will be used.

You can also create a custom signer class, as long as it impelements the `Risan\OAuth1\Signature\SignerInterface` interface.

## OAuth 1.0 Flow

In order to access a protected resource, the OAuth 1.0 flow can be broken down into four steps:

### Step 1: Obtaining Temporary Credentials

The very first step is to obtain the temporary credentials or mostly known as the access token. To obtain it, you need to call the `requestTemporaryCredentials` method on the `Risan\OAuth1\OAuth1` instance:

```php
$temporaryCredentials = $oauth1->requestTemporaryCredentials();
```

It will return an instance of `Risan\OAuth1\Credentials\TemporaryCredentials` class, which later you'll use to generate an authorization URI (Step 2) and to obtain the token credentials (Step 3).

### Step 2: Generate and Redirect User to Authorization URI

Once you've got the temporary credentials, the second step is to generate and redirect the user to the authorization page. This is where the user will be asked to grant their permission to your application. You need to pass the previously obtained `Risan\OAuth1\Credentials\TemporaryCredentials` class instance to the `buildAuthorizationUri` method to generate the authorization URI:

```php
$authorizationUri = $oauth1->buildAuthorizationUri($temporaryCredentials);

// Redirect user to the authorization URI.
header("Location: {$authorizationUri}");
exit();
```

### Step 3: Obtaining Token Credentials

The third step is to obtain the token credentials, or also known as the access token. Upon successful authorization, the provider will redirect the user to the defined callback URI along with at least two additional query parameters:

* `oauth_token`
* `oauth_verifier`

Along with the previously obtained temporary credentials, you'll need to pass these two query parameters to `requestTokenCredentials` method to obtain token credentials:

```php
$tokenCredentials = $oauth1->requestTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
```

This method will return an instance of `Risan\OAuth1\Credentials\TokenCredentials` class, which you're going to need to access the protected resource.

### Step 4: Accessing the Protected Resource

Finally, once you've got the token credentials instance, you can start making a request to the protected resource. Pass the obtained `Risan\OAuth1\Credentials\TokenCredentials` instance to the `setTokenCredentials` method before making any requests to the protected resource, or else an exception will be thrown.

```php
// Set the previously obtained token credentials.
$oauth1->setTokenCredentials($tokenCredentials);

// Make a request to the protected resource.
$response = $oauth1->request('GET', 'https://api.twitter.com/1.1/statuses/user_timeline.json');
```

The `request` method will return an instance of `Psr\Http\Message\ResponseInterface` class.

## Making HTTP Request

Once you've set the obtained token credentials with the `setTokenCredentials` method, you can start making the HTTP request to the protected API endpoints. You can use the `request` method for this purpose:

```php
$response = $oauth1->request($method, $uri, $options);
```

This method accepts three parameters:

* `method` (required): The HTTP method that you'd like to use (e.g. `GET`, `POST`, `PUT`, `PATCH`, `DELETE`)
* `uri` (required): The URI of the API endpoint that you'd like to access. You can also pass a relative URI as long as you pass the `base_uri` in the configuration array.
* `options` (optional): It's an optional array paramater to configure your request. It's the same [Request Options](https://guzzle.readthedocs.io/en/stable/request-options.html) that you'll pass when making an HTTP request using [Guzzle](https://guzzle.readthedocs.io). You can check all available options that you can pass on [Guzzle documentation](http://guzzle.readthedocs.io/en/stable/request-options.html).

There are also shortcut methods for common HTTP methods:

```php
// GET method
$oauth1->get($uri, $options);

// POST method
$oauth1->post($uri, $options);

// PUT method
$oauth1->put($uri, $options);

// PATCH method
$oauth1->patch($uri, $options);

// DELETE method
$oauth1->delete($uri, $options);
```

## Working with the Response

The `request` method will return an instance of `Psr\Http\Message\ResponseInterface`. You can check the returned status code with the `getStatusCode` method:

```php
echo $response->getStatusCode();
```

Or you can also get the headers on the returned response like so:

```php
// Get all of the headers
$headers = $response->getHeaders();

// Or get a specific header
$header = $response->getHeader('X-Foo');
```

And to get the response's body, you can use the `getBody` method. Note that it will return a `Psr\Http\Message\StreamInterface` instance. To convert the `StreamInterface` instace into a string, you can call the `getContents` method or simply cast it into a string.

```php
$bodyStream = $response->getBody();

// Get the string representation of the stream
$bodyStream = $bodyStream->getContents();

// Or simply cast it
$bodyString = (string) $bodyStream;
```

So if the API endpoint returns a JSON formatted response, you can covert the returned response into an associative array like this:

```php
$result = json_decode($response->getBody()->getContents(), true);
```

## Built-In Providers

This package also offers some third-party providers that you can use.

### Trello

Use the `trello` method to create an instance of `OAuth1` configured for Trello.

```php
$oauth1 = Risan\OAuth1\ProviderFactory::trello([
    'client_credentials_identifier' => 'YOUR_TRELLO_API_KEY',
    'client_credentials_secret' => 'YOUR_TRELLO_SECRET',
    'callback_uri' => 'YOUR_CALLBACK_URI',
]);
```

You can get both of your API key and secret from [Developer API Keys](https://trello.com/app-key) page. The base URI is set to `https://api.trello.com/1/` so you can use a relative URI instead:

```php
$response = $oauth1->request('GET', 'members/johndoe/cards');
```

### Tumblr

Use the `tumblr` method to create an instance of `OAuth1` configured for Tumblr.

```php
$oauth1 = Risan\OAuth1\ProviderFactory::tumblr([
    'client_credentials_identifier' => 'YOUR_TUMBLR_CONSUMER_KEY',
    'client_credentials_secret' => 'YOUR_TUMBLR_CONSUMER_SECRET',
    'callback_uri' => 'YOUR_CALLBACK_URI',
]);
```

You need to register a Tumblr application [here](https://www.tumblr.com/oauth/apps) to get the consumer key and secret. The base URI is set to `https://api.tumblr.com/v2/` so you can use a relative URI instead:

```php
$response = $oauth1->request('GET', 'user/info');
```

### Twitter

Use the `twitter` method to create an instance of `OAuth1` configured for Twitter.

```php
$oauth1 = Risan\OAuth1\ProviderFactory::twitter([
    'client_credentials_identifier' => 'YOUR_TWITTER_CONSUMER_KEY',
    'client_credentials_secret' => 'YOUR_TWITTER_CONSUMER_SECRET',
    'callback_uri' => 'YOUR_CALLBACK_URI',
]);
```

You need to register a Twitter application [here](https://apps.twitter.com) to get the consumer key and secret. The base URI is set to `https://api.twitter.com/1.1/` so you can use a relative URI instead:

```php
$response = $oauth1->request('GET', 'statuses/user_timeline.json');
```

### Upwork

Use the `upwork` method to create an instance of `OAuth1` configured for Upwork.

```php
$oauth1 = Risan\OAuth1\ProviderFactory::upwork([
    'client_credentials_identifier' => 'YOUR_API_KEY',
    'client_credentials_secret' => 'YOUR_API_SECRET',
    'callback_uri' => 'YOUR_CALLBACK_URI',
]);
```

You need to register a Upwork application [here](https://www.upwork.com/services/api/apply) to get the API key and secret. The base URI is set to `https://www.upwork.com/` so you can use a relative URI instead:

```php
$response = $oauth1->request('GET', 'api/auth/v1/info.json');
```

## License

MIT © [Risan Bagja Pradana](https://risan.io)
