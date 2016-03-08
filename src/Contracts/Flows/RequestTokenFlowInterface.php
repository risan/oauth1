<?php

namespace OAuth1\Contracts\Flows;

interface RequestTokenFlowInterface
{
    /**
     * Request token url.
     *
     * @return string
     */
    public function requestTokenUrl();

    /**
     * Get request token.
     *
     * @return \OAuth1\Contracts\Tokens\RequestTokenInterface
     */
    public function requestToken();

    /**
     * Get request token headers.
     *
     * @return array
     */
    public function requestTokenHeaders();
}
