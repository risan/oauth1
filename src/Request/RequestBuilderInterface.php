<?php

namespace Risan\OAuth1\Request;

interface RequestBuilderInterface
{
    /**
     * Get the ConfigInterface instance.
     *
     * @return \Risan\OAuth1\ConfigInterface
     */
    public function getConfig();

    /**
     * Get the SignerInterface instance.
     *
     * @return \Risan\OAuth1\Signature\SignerInterface
     */
    public function getSigner();

    /**
     * Get the NonceGeneratorInterface instance.
     *
     * @return \Risan\OAuth1\Request\NonceGeneratorInterface
     */
    public function getNonceGenerator();

    /**
     * Get current timestamp in seconds since Unix Epoch.
     *
     * @return int
     */
    public function getCurrentTimestamp();

    /**
     * Get base protocol parameters for the authorization header.
     *
     * @return array
     */
    public function getBaseProtocolParameters();

    /**
     * Add signature parameter to the given protocol parameters.
     *
     * @param array  &$parameters
     * @param string $uri
     * @param string $httpMethod
     */
    public function addSignatureParameter(array &$parameters, $uri, $httpMethod = 'POST');

    /**
     * Normalize protocol parameters to be used as authorization header.
     *
     * @param  array  $parameters
     * @return string
     */
    public function normalizeProtocolParameters(array $parameters);
}
