<?php

namespace Risan\OAuth1\Contracts\Flows;

use Risan\OAuth1\Contracts\Tokens\RequestTokenInterface;

interface AuthorizationFlowInterface
{
    /**
     * Authorize url.
     *
     * @return string
     */
    public function authorizeUrl();

    /**
     * Build authorization url.
     *
     * @param \OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     *
     * @return string
     */
    public function buildAuthorizationUrl(RequestTokenInterface $requestToken);
}
