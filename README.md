# OAuth1 Client Library

[![Build Status](https://img.shields.io/travis/risan/oauth1.svg?style=flat-square)](https://travis-ci.org/risan/oauth1)
[![HHVM Tested](https://img.shields.io/hhvm/risan/oauth1.svg?style=flat-square)](https://travis-ci.org/risan/oauth1)
[![Latest Stable Version](https://img.shields.io/packagist/v/risan/oauth1.svg?style=flat-square)](https://packagist.org/packages/risan/oauth1)
[![License](https://img.shields.io/packagist/l/risan/oauth1.svg?style=flat-square)](https://packagist.org/packages/risan/oauth1)

Simple, fluent and extensible OAuth 1 client library for PHP.

## Table of Contents

* [Dependencies](#dependencies)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Configuration](#configuration)
* [OAuth 1 Flow](#get-delivery-options)
  * [Get Request Token](#get-request-token)
  * [Authorize Access](#authorize-access)
  * [Get Access Token](#get-access-token)
  * [Access Protected Resource](#access-protected-resource)

## Dependencies

This package relies on the following library to work:

* [Guzzle](https://github.com/guzzle/guzzle)

All dependencies will be automatically downloaded if you are using [Composer](https://getcomposer.org/) to install this package.

## Installation

To install this library using [Composer](https://getcomposer.org/), simply run the following command inside your project directory:

```bash
composer require risan/oauth1
```

Or you may also add `risan\oauth1` package into your `composer.json` file like so:

```bash
"require": {
  "risan/oauth1": "~1.0"
}
```

And then don't forget to run the following command to install the library:

```bash
composer install
```

## Basic Usage

OAuth1 client library is very flexible, thus you may use this library for various providers that implement OAuth version 1 such as [Twitter](https://twitter.com) or [Tumblr](https://www.tumblr.com).

Here is some basic example of how to use OAuth1 client library to communicate with Twitter API.

```php
// Start session.
session_start();

// Include Composer autoload file.
require 'vendor/autoload.php';

// Create a new instance of OAuth1 client for Twitter.
$oauth1 = new OAuth1\OAuth1([
    'consumer_key'      => 'YOUR_TWITTER_CONSUMER_KEY',
    'consumer_secret'   => 'YOUR_TWITTER_CONSUMER_SECRET',
    'request_token_url' => 'https://api.twitter.com/oauth/request_token',
    'authorize_url'     => 'https://api.twitter.com/oauth/authorize',
    'access_token_url'  => 'https://api.twitter.com/oauth/access_token',
    'callback_url'      => 'YOUR_CALLBACK_URL', // Optional
    'resource_base_url' => 'https://api.twitter.com/1.1/'
]);

// STEP 4: ACCESS PROTECTED RESOURCE.
if (isset($_SESSION['access_token'])) {
    // Retrieve the saved AccessToken instance (see STEP 3).
    $accessToken = unserialize($_SESSION['access_token']);

    // Set access token.
    $oauth1->setGrantedAccessToken($accessToken);

    // Get authenticated user's timeline.
    // @return Psr\Http\Message\ResponseInterface instance
    $response = $oauth1->get('statuses/user_timeline.json');

    echo $response->getBody()->getContents();
}

// STEP 3: GET ACCESS TOKEN.
elseif (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
    // Retrieve the previously generated request token (see STEP 1).
    $requestToken = unserialize($_SESSION['request_token']);

    // Get access token.
    // @return OAuth1\Tokens\AccessToken instance
    $accessToken = $oauth1->accessToken($requestToken, $_GET['oauth_token'], $_GET['oauth_verifier']);

    // Serialize AccessToken instance and save it to session.
    $_SESSION['access_token'] = serialize($accessToken);

    // No longer need request token.
    unset($_SESSION['request_token']);

    // Reload page.
    header("Location: {$_SERVER['PHP_SELF']}");
}

// STEP 1: Get request token.
// STEP 2: Authorize access.
else {
    // Get request token.
    // @return OAuth1\Tokens\RequestToken instance
    $requestToken = $oauth1->requestToken();

    // Serialize RequestToken instance and save to session.
    $_SESSION['request_token'] = serialize($requestToken);

    // Authorize access.
    $oauth1->authorize($requestToken);
}
```

## Configuration

To create an instance of `OAuth1\Oauth1` class you have to pass an instance of `OAuth1\Config` class as an argument. For ease of use, you may also pass an array that reflects your OAuth 1 provider configuration. Your configuration array should be an associative array that contains the following keys:

* `consumer_key` (String)

  Your OAuth provider's consumer key, sometimes it is also called API Key.

* `consumer_secret` (String)

  Your OAuth provider's consumer secret, sometimes it is also called API Secret.

* `request_token_url` (String)

  Your OAuth provider's request token url. This is an endpoint where your application will request for a temporary token (request token) from your provider.

* `authorize_url` (String)

  Your OAuth provider's authorize url. This is a url where your OAuth provider will ask user's permission to grant access for your application.

* `access_token_url` (String)

  Your OAuth provider's access token url. This is an endpoint where your application can request for an access token. This access token will be used by your application to access protected resources.

* `callback_url` (String) *Optional*

  Your OAuth callback url. This is a url in your application, where your OAuth provider will redirect user after he/she granted the permission.

* `resource_base_url` (String)

  Your provider's protected resource base url. This is base url for the protected API endpoints.

For example, if you are going to use Twitter API, you'll have a configuration like this:

```php
$twitter = new OAuth1\OAuth1([
    'consumer_key'      => 'YOUR_TWITTER_CONSUMER_KEY',
    'consumer_secret'   => 'YOUR_TWITTER_CONSUMER_SECRET',
    'request_token_url' => 'https://api.twitter.com/oauth/request_token',
    'authorize_url'     => 'https://api.twitter.com/oauth/authorize',
    'access_token_url'  => 'https://api.twitter.com/oauth/access_token',
    'callback_url'      => 'YOUR_CALLBACK_URL', // Optional
    'resource_base_url' => 'https://api.twitter.com/1.1/'
]);
```

## OAuth 1 Flow

### Get Request Token

### Authorize Access

### Get Access Token

### Access Protected Resource
