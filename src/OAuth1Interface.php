<?php

namespace Risan\OAuth1;

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
     * Obtain the temporary credentials.
     *
     * @return \Risan\OAuth1\Credentials\TemporaryCredentials
     */
    public function getTemporaryCredentials();
}
