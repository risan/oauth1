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
     * Request authorization.
     *
     * @param OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     */
    public function authorize(RequestTokenInterface $requestToken)
    {
        header('Location: '.$this->buildAuthorizationUrl($requestToken));

        exit();
    }

    /**
     * Build authorization url.
     *
     * @param OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
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
}
