<?php

namespace OAuth1Client\Contracts;

use OAuth1Client\Contracts\OAuth1Flows\AuthorizationFlowInterface;
use OAuth1Client\Contracts\OAuth1Flows\TokenCredentialsFlowInterface;
use OAuth1Client\Contracts\OAuth1Flows\TemporaryCredentialsFlowInterface;

interface OAuth1ClientInterface extends TemporaryCredentialsFlowInterface,
                                        AuthorizationFlowInterface,
                                        TokenCredentialsFlowInterface {
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
