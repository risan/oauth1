<?php

namespace Risan\OAuth1\Request;

use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

interface ProtocolParameterInterface
{
    /**
     * Get the ConfigInterface instance.
     *
     * @return \Risan\OAuth1\Config\ConfigInterface
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
     * Get the current timestamp in seconds since Unix Epoch.
     *
     * @return int
     */
    public function getCurrentTimestamp();

    /**
     * Get the OAuth1 protocol version.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Get the base protocol parameters.
     *
     * @return array
     */
    public function getBase();

    /**
     * Create the signature.
     *
     * @param array $protocolParameters
     * @param string uri
     * @param \Risan\OAuth1\Credentials\ServerIssuedCredentials|null $serverIssuedCredentials
     * @param array                                                  $requestOptions
     * @param string $httpMethod
     *
     * @return string
     */
    public function getSignature(array $protocolParameters, $uri, ServerIssuedCredentials $serverIssuedCredentials = null, array $requestOptions = [], $httpMethod = 'POST');

    /**
     * Get protocol parameters for obtaining temporary credentials.
     *
     * @return array
     */
    public function forTemporaryCredentials();

    /**
     * Get protocol parameters for obtaining token credentials.
     *
     * @param \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @param string                                         $verificationCode
     *
     * @return array
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode);

    /**
     * Get protocol parameters for accessing protected resource.
     *
     * @param \Risan\OAuth1\Credentials\TokenCredentials $tokenCredentials
     * @param string                                     $httpMethod
     * @param string                                     $uri
     * @param array                                      $requestOptions
     *
     * @return array
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, $httpMethod, $uri, array $requestOptions = []);
}
