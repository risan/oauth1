<?php

namespace OAuth1Client\Contracts\OAuth1Flows;

use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

interface AuthorizationFlowInterface {
    /**
     * Request authorization.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @return void
     */
    public function authorize(TemporaryCredentialsInterface $temporaryCredentials);

    /**
     * Build authorization url.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @return string
     */
    public function buildAuthorizationUrl(TemporaryCredentialsInterface $temporaryCredentials);

    /**
     * Authorization url.
     *
     * @return string
     */
    public function authorizationUrl();
}
