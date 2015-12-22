<?php

namespace OAuth1Client;

use OAuth1Client\Contracts\OAuth1ClientInterface;
use OAuth1Client\Credentials\TemporaryCredentials;

class Client implements OAuth1ClientInterface {
    /**
     * Http client instance.
     *
     * @var OAuth1Client\Contracts\OAuth1ClientInterface
     */
    protected $httpClient;

    /**
     * Get http client instance.
     *
     * @return OAuth1Client\Contracts\HttpClientInterface
     */
    public function httpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }

        return $this->httpClient;
    }

    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials()
    {
        return new TemporaryCredentials('foo', 'bar');
    }
}
