<?php

namespace OAuth1;

use InvalidArgumentException;
use OAuth1\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * OAuth consumer key.
     *
     * @var string
     */
    protected $consumerKey;

    /**
     * OAuth consumer secret.
     *
     * @var string
     */
    protected $consumerSecret;

    /**
     * OAuth request token url.
     *
     * @var string
     */
    protected $requestTokenUrl;

    /**
     * OAuth authorize url.
     *
     * @return string
     */
    protected $authorizeUrl;

    /**
     * OAuth access token url.
     *
     * @var string
     */
    protected $accessTokenUrl;

    /**
     * OAuth callback url.
     *
     * @var string|null
     */
    protected $callbackUrl;

    /**
     * Resource base url.
     *
     * @return string|null
     */
    protected $resourceBaseUrl;

    /**
     * Create a new instance of Config.
     *
     * @param string      $consumerKey
     * @param string      $consumerSecret
     * @param string      $requestTokenUrl
     * @param string      $authorizeUrl
     * @param string      $accessTokenUrl
     * @param string|null $callbackUrl
     * @param string|null $resourceBaseUrl
     */
    public function __construct($consumerKey, $consumerSecret, $requestTokenUrl, $authorizeUrl, $accessTokenUrl, $callbackUrl = null, $resourceBaseUrl = null)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->requestTokenUrl = $requestTokenUrl;
        $this->authorizeUrl = $authorizeUrl;
        $this->accessTokenUrl = $accessTokenUrl;
        $this->callbackUrl = $callbackUrl;
        $this->resourceBaseUrl = $resourceBaseUrl;
    }

    /**
     * Get OAuth consumer key.
     *
     * @return string
     */
    public function consumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Get OAuth consumer secret.
     *
     * @return string
     */
    public function consumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Get OAuth request token url.
     *
     * @return string
     */
    public function requestTokenUrl()
    {
        return $this->requestTokenUrl;
    }

    /**
     * Get OAuth authorize url.
     *
     * @return string
     */
    public function authorizeUrl()
    {
        return $this->authorizeUrl;
    }

    /**
     * Get OAuth access token url.
     *
     * @return string
     */
    public function accessTokenUrl()
    {
        return $this->accessTokenUrl;
    }

    /**
     * Get OAuth callback url.
     *
     * @return string|null
     */
    public function callbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Ge resource base url.
     *
     * @return string|null
     */
    public function resourceBaseUrl()
    {
        return $this->resourceBaseUrl;
    }

    /**
     * Set resource base url.
     *
     * @param string $url
     *
     * @return \OAuth1\Contracts\ConfigInterface
     */
    public function setResourceBaseUrl($url)
    {
        $this->resourceBaseUrl = $url;

        return $this;
    }

    /**
     * Create an instance from array.
     *
     * @param array $config
     */
    public static function fromArray(array $config)
    {
        $requiredParams = [
            'consumer_key',
            'consumer_secret',
            'request_token_url',
            'authorize_url',
            'access_token_url',
        ];

        foreach ($requiredParams as $param) {
            if (!isset($config[$param])) {
                throw new InvalidArgumentException("Missing OAuth1 client configuration: $param.");
            }
        }

        $callbackUrl = isset($config['callback_url']) ? $config['callback_url'] : null;
        $resourceBaseUrl = isset($config['resource_base_url']) ? $config['resource_base_url'] : null;

        return new static(
            $config['consumer_key'],
            $config['consumer_secret'],
            $config['request_token_url'],
            $config['authorize_url'],
            $config['access_token_url'],
            $callbackUrl,
            $resourceBaseUrl
        );
    }
}
