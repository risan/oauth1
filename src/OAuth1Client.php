<?php

namespace OAuth1Client;

use OAuth1Client\Signatures\HMACSHA1Signature;
use OAuth1Client\Contracts\OAuth1ClientInterface;
use OAuth1Client\Credentials\TemporaryCredentials;
use OAuth1Client\Contracts\Signatures\SignatureInterface;
use OAuth1Client\Contracts\Credentials\ClientCredentialsInterface;

abstract class OAuth1Client implements OAuth1ClientInterface {
    /**
     * Http client instance.
     *
     * @var OAuth1Client\Contracts\OAuth1ClientInterface
     */
    protected $httpClient;

    /**
     * Client credentials instance.
     *
     * @var OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    protected $clientCredentials;

    /**
     * Signature instance.
     *
     * @var OAuth1Client\Contracts\Signatures\SignatureInterface
     */
    protected $signature;

    /**
     * Create a new instance of OAuth1Client.
     *
     * @param OAuth1Client\Contracts\Credentials\ClientCredentialsInterface $clientCredentials
     * @param OAuth1Client\Contracts\Signatures\SignatureInterface|null     $signature
     */
    public function __construct(ClientCredentialsInterface $clientCredentials, SignatureInterface $signature = null)
    {
        $this->clientCredentials = $clientCredentials;
        $this->signature = $signature;
    }

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
     * Get client credential.
     *
     * @return OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    public function clientCredentials()
    {
        return $this->clientCredentials;
    }

    /**
     * Get signature.
     *
     * @return OAuth1Client\Contracts\Signatures\SignatureInterface
     */
    public function signature()
    {
        if (is_null($this->signature)) {
            $this->signature = new HMACSHA1Signature($this->clientCredentials());
        }

        return $this->signature;
    }

    /**
     * Generate random nonce.
     *
     * @return string
     */
    public function nonce()
    {
        return md5(mt_rand());
    }

    /**
     * Get current timestamp.
     *
     * @return int
     */
    public function timestamp()
    {
        return time();
    }

    /**
     * Get OAuth version.
     *
     * @return string
     */
    public function version()
    {
        return '1.0';
    }

    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials()
    {
        $response = $this->httpClient()->post($this->temporaryCredentialsUrl(), [
            'headers' => $this->temporaryCredentialsHeaders()
        ]);

        return TemporaryCredentials::fromHttpResponse($response);
    }

    protected function temporaryCredentialsHeaders()
    {
        $parameters = $this->baseProtocolParameters();

        $parameters['oauth_signature'] = $this->signature()->sign($this->temporaryCredentialsUrl(), $parameters);

        return [
            'Authorization' => $this->authorizationHeader($parameters)
        ];
    }

    protected function baseProtocolParameters()
    {
        return [
            'oauth_consumer_key' => $this->clientCredentials()->identifier(),
            'oauth_nonce' => $this->nonce(),
            'oauth_signature_method' => $this->signature()->method(),
            'oauth_timestamp' => $this->timestamp(),
            'oauth_version' => $this->version(),
        ];
    }

    protected function authorizationHeader(array $parameters)
    {
        $parameters = http_build_query($parameters, '', ', ', PHP_QUERY_RFC3986);

        return "OAuth $parameters";
    }
}
