<?php

namespace OAuth1\Contracts\Flows;

use OAuth1\Contracts\Tokens\RequestTokenInterface;

interface AuthorizationFlowInterface {
    /**
     * Authorization url.
     *
     * @return string
     */
    public function authorizationUrl();

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
