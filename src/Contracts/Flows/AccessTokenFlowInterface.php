<?php

namespace OAuth1\Contracts\Flows;

use OAuth1\Contracts\Tokens\RequestTokenInterface;

interface AccessTokenFlowInterface
{
    /**
     * Access token url.
     *
     * @return string
     */
    public function accessTokenUrl();

    /**
     * Get access token.
     *
     * @param OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     * @param string                                        $tokenKey
     * @param string                                        $verifier
     *
     * @return OAuth1\Contracts\Tokens\AccessTokenInterface
     */
    public function accessToken(RequestTokenInterface $requestToken, $tokenKey, $verifier);

    /**
     * Is valid token?
     *
     * @param OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param string                                       $tokenKey
     *
     * @return bool
     */
    public function isValidToken(RequestTokenInterface $requestToken, $tokenKey);

    /**
     * Access token header.
     *
     * @param OAuth1\Contracts\Token\RequestTokenInterface $requestToken
     * @param string                                       $verifier
     *
     * @return array
     */
    public function accessTokenHeaders(RequestTokenInterface $requestToken, $verifier);
}
