<?php

namespace OAuth1\Contracts\Flows;

use OAuth1\Contracts\Tokens\RequestTokenInterface;

interface AuthorizationFlowInterface {
    /**
     * Authorize url.
     *
     * @return string
     */
    public function authorizeUrl();

    /**
     * Request authorization.
     *
     * @param  OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     * @return void
     */
    public function authorize(RequestTokenInterface $requestToken);

    /**
     * Build authorization url.
     *
     * @param  OAuth1\Contracts\Tokens\RequestTokenInterface $requestToken
     * @return string
     */
    public function buildAuthorizationUrl(RequestTokenInterface $requestToken);
}
