# OAuth1 Client Library

[![Build Status](https://travis-ci.org/risan/oauth1.svg?branch=master)](https://travis-ci.org/risan/oauth1)
[![HHVM Status](http://hhvm.h4cc.de/badge/risan/oauth1.svg?style=flat)](http://hhvm.h4cc.de/package/risan/oauth1)
[![StyleCI](https://styleci.io/repos/48460990/shield?style=flat)](https://styleci.io/repos/48460990)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/risan/oauth1/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/risan/oauth1/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/risan/oauth1/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/risan/oauth1/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/risan/oauth1/v/stable)](https://packagist.org/packages/risan/oauth1)
[![License](https://poser.pugx.org/risan/oauth1/license)](https://packagist.org/packages/risan/oauth1)

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
* [Built In Providers](#built-in-providers)
  * [Twitter](#twitter)
  * [Tumblr](#tumblr)
  * [Upwork](#upwork)

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
  "risan/oauth1": "~1.2"
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

OAuth version 1 can be broken down into four distinct steps in order to access protected resources.

### Get Request Token

Step 1 is to retrieve for a request token. A request token is used as a temporary credentials for generating authorization url. To retrieve a request token from your OAuth provider, simply call `requestToken()` method like so:

```php
$requestToken = $oauth1->requestToken();
```

This `requestToken()` method will return an instance of `OAuth`\Tokens\RequestToken` class which will be needed in the authorization step.

### Authorize Access

Step 2 is to authorize access. Once you have the request token, the next step is to ask user's permission to grant access for your application. To redirect user to your OAuth provider's authorization page, you need to call `authorize()` method:

```php
$oauth1->authorize(OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken);
```

This method requires an argument which must confront the `RequestTokenInterface`. You may pass an instance of `OAuth1\Tokens\RequestToken` class that you get from the `requestToken()` previously.

This method will not return anything, because it simpy redirects user to authorization page that is provided by your OAuth provider.

### Get Access Token

Step 3 is to get an access token. Once the user has granted his/her permission, he/she will be redirected back to the configured callback url with the additional query string:

* `oauth_token`
* `oauth_verifier`

You will need these returned two paramters to verify and request for access token from OAuth provider. To get an access token, you need to call `accessToken()` method:

```php
$accessToken = $oauth1->accessToken(OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken, $tokenKey, $verifier);
```

The `accessToken()` method requires an instance that implements `RequestTokenInterface`. You may pass the `OAuth1\Tokens\RequestToken` class instance which retrieved from step 2. The method is also required `$tokenKey` and `$verifier` arguments. This two arguments are retrieved from the query string like so:

```php
$accessToken = $oauth1->accessToken($requestToken, $_GET['oauth_token'], $_GET['oauth_verifier']);
```

The `accessToken()` method will return an instance of `OAuth1\Tokens\AccessToken` class. This `AccessToken` instance will then be used to access protected resources.

### Access Protected Resource

Finally, the last step is to access protected resources! Once you've got the access token, you can start accessing protected resources. First you need to set the granted access token like so:

```php
$oauth1->setGrantedAccessToken(OAuth1\Contracts\Tokens\AccessTokenInterface $accessToken);
```

Once the access token is set, you can start sending HTTP request to the protected API endpoints using `request()` method:

```php
$response = $oauth->request($method, $url, array $options = []);
```

The `request()` method has three arguments:

* `$method` (String)

  This is the HTTP method to use: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, or `HEAD`.

* `$url` (String)

  This is the url or the path of the API endpoint. Note that we have the `resource_base_url` in configuration array, so you don't have to specify a full path url.

* `$options` (Array) *Optional*

  This is a requests options to send along with. You may see the full reference of requests options in [Guzzle requests options documentation](http://docs.guzzlephp.org/en/latest/request-options.html).

The `request()` method will return an instance of `Psr\Http\Message\ResponseInterface`. You may read further about the retured response object in [Guzzle and PSR-7 documentation](http://docs.guzzlephp.org/en/latest/psr7.html#responses).

You may also send HTTP request using various shorthand methods:

```php
// HTTP GET.
$oauth->get($url, array $options = []);

// HTTP POST.
$oauth->post($url, array $options = []);

// HTTP PUT.
$oauth->put($url, array $options = []);

// HTTP PATCH.
$oauth->patch($url, array $options = []);

// HTTP DELETE.
$oauth->delete($url, array $options = []);

// HTTP OPTIONS.
$oauth->options($url, array $options = []);

// HTTP HEAD.
$oauth->head($url, array $options = []);
```

## Built In Providers

OAuth1 library has built in support for the following providers:

* [Twitter](https://twitter.com/)
* [Tumblr](https://tumblr.com/)
* [Upwork](https://upwork.com/)

With this providers, you only have to pass `consumer_key` and `consumer_secret` as a configuration array when creating a client instance.

### Twitter

You can the `Oauth1\Providers\Twitter` class to communicate with Twitter Rest API. To create an instance of this class:

```php
$twitter = new OAuth1\Providers\Twitter([
    'consumer_key'    => 'YOUR_TWITTER_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_TWITTER_CONSUMER_SECRET',
    'callback_url'    => 'YOUR_CALLBACK_URL' // Optional
]);
```

Once, you've got the access token, you can use this Twitter client instance to retrieve protected resources. For example to retrieve current user's timeline:

```php
$twitter->setGrantedAccessToken($accessToken);

$response = $twitter->get('statuses/user_timeline.json');
```

### Tumblr

You can use the provided `Oauth1\Providers\Tumblr` class to communicate with Tumblr API. To instantiate it:

```php
$tumblr = new OAuth1\Providers\Tumblr([
    'consumer_key'    => 'YOUR_TUMBLR_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_TUMBLR_CONSUMER_SECRET',
    'callback_url'    => 'YOUR_CALLBACK_URL' // Optional
]);
```

Once the user has granted the permission, you can set the given access token and perform a request to Tumblr API. For example, we can retrieve the Tumblr blog information like so:

```php
$tumblr->setGrantedAccessToken($accessToken);

$response = $tumblr->get('blog/allthingseurope.tumblr.com/info');
```

### Upwork

You can also use the `OAuth\Providers\Upwork` class to communicate with Upwork API. To instantiate the `Upwork` client:

```php
$upwork = new OAuth1\Providers\Upwork([
    'consumer_key'    => 'YOUR_UPWORK_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_UPWORK_CONSUMER_SECRET',
    'callback_url'    => 'YOUR_CALLBACK_URL' // Optional
]);
```

Once you've got the access token, you may now easily access the Upwork API. For example, if we are about to retrieve the authenticated user information, we can do the following:

```php
$upwork->setGrantedAccessToken($accessToken);

$response = $upwork->get('api/auth/v1/info.json');
```
