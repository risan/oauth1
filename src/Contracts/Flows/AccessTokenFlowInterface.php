<?php

namespace OAuth1\Contracts\Flows;

use OAuth1\Contracts\Token\RequestTokenInterface;

interface AccessTokenFlowInterface {
    /**
     * Access token url.
     *
     * @return string
     */
    public function accessTokenUrl();

    /**
     * Get access token.
     *
     * @param  OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param  string   $tokenKey
     * @param  string   $verifier
     * @return OAuth1\Contracts\Token\AccessTokenInterface
     */
    public function accessToken(RequestTokenInterface $requestToken, $tokenKey, $verifier);

    /**
     * Is valid token?
     *
     * @param  OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param  string   $tokenKey
     * @return boolean
     */
    public function isValidToken(RequestTokenInterface $requestToken, $tokenKey);

    /**
     * Access token header.
     *
     * @param  OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param  string   $verifier
     * @return array
     */
    public function accessTokenHeaders(RequestTokenInterface $requestToken, $verifier);
}
