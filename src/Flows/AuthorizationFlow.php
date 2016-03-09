<?php

namespace OAuth1\Flows;

use OAuth1\Contracts\Tokens\RequestTokenInterface;

trait AuthorizationFlow
{
    /**
     * Authorize url.
     *
     * @return string
     */
    public function authorizeUrl()
    {
        return $this->config()->authorizeUrl();
    }

    /**
     * Build authorization url.
     *
     * @param \OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     *
     * @return string
     */
    public function buildAuthorizationUrl(RequestTokenInterface $requestToken)
    {
        $query = http_build_query([
            'oauth_token' => $requestToken->key(),
        ]);

        return $this->authorizeUrl().'?'.$query;
    }

    /**
     * Get client configuration.
     *
     * @return \OAuth1\Contracts\ConfigInterface
     */
    abstract public function config();
}
