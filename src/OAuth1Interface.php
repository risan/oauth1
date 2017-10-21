<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Credentials\TemporaryCredentials;

interface OAuth1Interface
{
    /**
     * Get the HttpClientInterface instance.
     *
     * @return \Risan\OAuth1\HttpClientInterface
     */
    public function getHttpClient();

    /**
     * Get the RequestFactoryInterface instance.
     *
     * @return \Risan\OAuth1\Request\RequestFactoryInterface
     */
    public function getRequestFactory();

    /**
     * Get the CredentialsFactoryInterface instance.
     *
     * @return \Risan\OAuth1\Credentials\CredentialsFactoryInterface
     */
    public function getCredentialsFactory();

    /**
     * Obtain the temporary credentials.
     *
     * @return \Risan\OAuth1\Credentials\TemporaryCredentials
     */
    public function getTemporaryCredentials();

    /**
     * Build the authorization URI.
     *
     * @param  \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @return string
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials);

    /**
     * Obtain the token credentials.
     *
     * @param  \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @param  string $temporaryIdentifier
     * @param  string $verificationCode
     * @return \Risan\OAuth1\Credentials\TokenCredentials
     * @throws \InvalidArgumentException
     */
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verificationCode);
}
