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
* [OAuth 1.0 Flow](#oauth-10-flow)
    * [Step 1: Obtaining Temporary Credentials](#step-1-obtaining-temporary-credentials)
    * [Step 2: Generate and Redirect User to Authorization URI](#step-2-generate-and-redirect-user-to-authorization-uri)
    * [Step 3: Obtaining Token Credentials](#step-3-obtaining-token-credentials)
    * [Step 4: Accessing the Protected Resource](#step-4-accessing-the-protected-resource)

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
