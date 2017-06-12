<?php

namespace Risan\OAuth1\Contracts;

interface ConfigInterface
{
    /**
     * Get OAuth consumer key.
     *
     * @return string
     */
    public function consumerKey();

    /**
     * Get OAuth consumer secret.
     *
     * @return string
     */
    public function consumerSecret();

    /**
     * Get OAuth callback url.
     *
     * @return string|null
     */
    public function callbackUrl();

    /**
     * Get OAuth request token url.
     *
     * @return string
     */
    public function requestTokenUrl();

    /**
     * Get OAuth authorize url.
     *
     * @return string
     */
    public function authorizeUrl();

    /**
     * Get OAuth access token url.
     *
     * @return string
     */
    public function accessTokenUrl();

    /**
     * Get resource base url.
     *
     * @return string|null
     */
    public function resourceBaseUrl();

    /**
     * Set resource base url.
     *
     * @param string $url
     *
     * @return \OAuth\ConfigInterface
     */
    public function setResourceBaseUrl($url);

    /**
     * Create an instance from array.
     *
     * @param array $config
     */
    public static function fromArray(array $config);
}
