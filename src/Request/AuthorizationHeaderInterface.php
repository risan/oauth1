<?php

namespace Risan\OAuth1\Request;

use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;

interface AuthorizationHeaderInterface
{
    /**
     * Get the ProtocolParameterInterface instance.
     *
     * @return \Risan\OAuth1\Request\ProtocolParameterInterface
     */
    public function getProtocolParameter();

    /**
     * Get the ConfigInterface instance.
     *
     * @return \Risan\OAuth1\Config\ConfigInterface
     */
    public function getConfig();

    /**
     * Get authorization header for obtaining temporary credentials.
     *
     * @return string
     */
    public function forTemporaryCredentials();

    /**
     * Get authorization header for obtaining token credentials.
     *
     * @param  \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @param  string $verificationCode
     * @return string
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode);

    /**
     * Get authorization header for accessing protected resource.
     *
     * @param  \Risan\OAuth1\Credentials\TokenCredentials $tokenCredentials
     * @param  string $httpMethod
     * @param  string $uri
     * @param  array $requestOptions
     * @return string
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, $httpMethod, $uri, array $requestOptions = []);

    /**
     * Normalize protocol parameters to be used as HTTP authorization header.
     *
     * @param  array  $parameters
     * @return string
     */
    public function normalizeProtocolParameters(array $parameters);
}
