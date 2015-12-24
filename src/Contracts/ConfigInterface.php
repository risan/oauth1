<?php

namespace OAuth1\Contracts;

interface ConfigInterface {
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
     * Get OAuth access token url.
     *
     * @return string
     */
    public function accessTokenUrl();

    /**
     * Create an instance from array.
     *
     * @param array $config
     */
    static public function fromArray(array $config);
}
