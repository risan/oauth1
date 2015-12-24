<?php

namespace OAuth1\Flows;

use InvalidArgumentException;
use OAuth1\Tokens\AccessToken;
use OAuth1\Contracts\Tokens\RequestTokenInterface;

trait AccessTokenFlow {
    /**
     * Access token url.
     *
     * @return string
     */
    public function accessTokenUrl()
    {
        return $this->config()->accessTokenUrl();
    }

    /**
     * Get access token.
     *
     * @param  OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     * @param  string   $tokenKey
     * @param  string   $verifier
     * @return OAuth1\Contracts\Tokens\AccessTokenInterface
     */
    public function accessToken(RequestTokenInterface $requestToken, $tokenKey, $verifier)
    {
        if (! $this->isValidToken($requestToken, $tokenKey)) {
            throw new InvalidArgumentException('The received oauth token does not match.');
        }

        $response = $this->httpClient()->post($this->accessTokenUrl(), [
            'headers' => $this->accessTokenHeaders($requestToken, $verifier)
        ]);

        return AccessToken::fromHttpResponse($response);
    }

    /**
     * Is valid token?
     *
     * @param  OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param  string   $tokenKey
     * @return boolean
     */
    public function isValidToken(RequestTokenInterface $requestToken, $tokenKey)
    {
        return $requestToken->key() === $tokenKey;
    }

    /**
     * Access token header.
     *
     * @param  OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param  string   $verifier
     * @return array
     */
    public function accessTokenHeaders(RequestTokenInterface $requestToken, $verifier)
    {
        $parameters = $this->baseProtocolParameters();

        $parameters['oauth_token'] = $requestToken->key();
        $parameters['oauth_verifier'] = $verifier;
        $parameters['oauth_signature'] = $this->signer()->setTokenSecret($requestToken->secret())->sign($this->accessTokenUrl(), $parameters);

        return [
            'Authorization' => $this->authorizationHeaders($parameters)
        ];
    }
}
