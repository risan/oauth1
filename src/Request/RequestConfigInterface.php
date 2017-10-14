<?php

namespace Risan\OAuth1\Request;

use Risan\OAuth1\Credentials\TemporaryCredentials;

interface RequestConfigInterface
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
     * Get URL for obtaining temporary credentials.
     *
     * @return string
     */
    public function getTemporaryCredentialsUrl();

    /**
     * Get authorization header for obtaining temporary credentials.
     *
     * @return string
     */
    public function getTemporaryCredentialsAuthorizationHeader();

    /**
     * Build the authorization URL.
     *
     * @param  Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @return string
     */
    public function buildAuthorizationUrl(TemporaryCredentials $temporaryCredentials);

    /**
     * Get URL for obtaining token credentials.
     *
     * @return string
     */
    public function getTokenCredentialsUrl();

    /**
     * Get authorization header for obtaining token credentials.
     *
     * @param  Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @param  string $verificationCode
     * @return string
     */
    public function getTokenCredentialsAuthorizationHeader(TemporaryCredentials $temporaryCredentials, $verificationCode);
}
