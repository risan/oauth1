<?php

namespace OAuth1Client\OAuth1Flows;

use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

trait AuthorizationFlow {
    /**
     * Request authorization.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @return void
     */
    public function authorize(TemporaryCredentialsInterface $temporaryCredentials)
    {
        header('Location: ' . $this->buildAuthorizationUrl($temporaryCredentials));

        exit();
    }

    /**
     * Build authorization url.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @return string
     */
    public function buildAuthorizationUrl(TemporaryCredentialsInterface $temporaryCredentials)
    {
        $query = http_build_query([
            'oauth_token' => $temporaryCredentials->identifier()
        ]);

        return $this->authorizationUrl() . '?' . $query;
    }
}
