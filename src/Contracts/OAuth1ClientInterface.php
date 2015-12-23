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
     * Get client credential.
     *
     * @return OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    public function clientCredentials();

    /**
     * Get signature.
     *
     * @return OAuth1Client\Contracts\Signatures\SignatureInterface
     */
    public function signature();

    /**
     * Generate random nonce.
     *
     * @return string
     */
    public function nonce();

    /**
     * Get current timestamp.
     *
     * @return int
     */
    public function timestamp();

    /**
     * Get OAuth version.
     *
     * @return string
     */
    public function version();

    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials();

    /**
     * Temporary credentials url.
     *
     * @return string
     */
    public function temporaryCredentialsUrl();

    /**
     * Temporary credentials header.
     *
     * @return array
     */
    public function temporaryCredentialsHeaders();

    /**
     * Base protocol parameters.
     *
     * @return array
     */
    public function baseProtocolParameters();

    /**
     * Build authorization headers.
     *
     * @param  array  $parameters
     * @return string
     */
    public function authorizationHeaders(array $parameters);
}
