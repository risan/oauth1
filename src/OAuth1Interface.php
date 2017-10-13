<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Credentials\TemporaryCredentials;

interface OAuth1Interface
{
    /**
     * Get the RequestConfigInterface instance.
     *
     * @return \Risan\OAuth1\ConfigInterface
     */
    public function getRequestConfig();

    /**
     * Get the HttpClientInterface instance.
     *
     * @return \Risan\OAuth1\HttpClientInterface
     */
    public function getHttpClient();

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
     * Build the authorization URL.
     *
     * @param  Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @return string
     */
    public function buildAuthorizationUrl(TemporaryCredentials $temporaryCredentials);
}
