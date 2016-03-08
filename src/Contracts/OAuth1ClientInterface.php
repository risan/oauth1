<?php

namespace OAuth1\Contracts;

use OAuth1\Contracts\Flows\AccessTokenFlowInterface;
use OAuth1\Contracts\Flows\AuthorizationFlowInterface;
use OAuth1\Contracts\Flows\GrantedFlowInterface;
use OAuth1\Contracts\Flows\RequestTokenFlowInterface;

interface OAuth1ClientInterface extends GrantedFlowInterface, AccessTokenFlowInterface, RequestTokenFlowInterface, AuthorizationFlowInterface
{
    /**
     * Get http client instance.
     *
     * @return \OAuth1\Contracts\HttpClientInterface
     */
    public function httpClient();

    /**
     * Get client configuration.
     *
     * @return \OAuth1\Contracts\ConfigInterface
     */
    public function config();

    /**
     * Get signer.
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    public function signer();

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
     * Get OAuth base protocol parameters.
     *
     * @return array
     */
    public function baseProtocolParameters();

    /**
     * Build authorization headers.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function authorizationHeaders(array $parameters);
}
