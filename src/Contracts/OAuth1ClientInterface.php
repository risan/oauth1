<?php

namespace OAuth1Client\Contracts;

interface OAuth1ClientInterface {
    /**
     * Get http client instance.
     *
     * @return OAuth1Client\Contracts\HttpClientInterface
     */
    public function httpClient();

    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials();
}
