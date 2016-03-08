<?php

namespace OAuth1;

use InvalidArgumentException;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\OAuth1ClientInterface;
use OAuth1\Contracts\Signers\SignerInterface;
use OAuth1\Flows\AccessTokenFlow;
use OAuth1\Flows\AuthorizationFlow;
use OAuth1\Flows\GrantedFlow;
use OAuth1\Flows\RequestTokenFlow;
use OAuth1\Signers\HmacSha1Signer;

class OAuth1 implements OAuth1ClientInterface
{
    use GrantedFlow,
        AccessTokenFlow,
        RequestTokenFlow,
        AuthorizationFlow;

    /**
     * Http client instance.
     *
     * @return \OAuth1\Contracts\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Client configuration.
     *
     * @var \OAuth1\Contracts\ConfigInterface
     */
    protected $config;

    /**
     * OAuth signer instance.
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    protected $signer;

    /**
     * Create a new instance of Generic class.
     *
     * @param \OAuth1\Contracts\ConfigInterface|array        $config
     * @param \OAuth1\Contracts\Signers\SignerInterface|null $signature
     */
    public function __construct($config, SignerInterface $signature = null)
    {
        if (is_array($config)) {
            $config = Config::fromArray($config);
        } elseif (!$config instanceof ConfigInterface) {
            throw new InvalidArgumentException('OAuth1 client configuration must be a valid array or an instance of OAuth1\Config class.');
        }

        $this->config = $config;
        $this->signature = $signature;
    }

    /**
     * Get http client instance.
     *
     * @return \OAuth1\Contracts\HttpClientInterface
     */
    public function httpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }

        return $this->httpClient;
    }

    /**
     * Get client configuration.
     *
     * @return \OAuth1\Contracts\ConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * Get signer.
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    public function signer()
    {
        if (is_null($this->signer)) {
            $this->signer = new HmacSha1Signer($this->config()->consumerSecret());
        }

        return $this->signer;
    }

    /**
     * Generate random nonce.
     *
     * @return string
     */
    public function nonce()
    {
        return md5(mt_rand());
    }

    /**
     * Get current timestamp.
     *
     * @return int
     */
    public function timestamp()
    {
        return time();
    }

    /**
     * Get OAuth version.
     *
     * @return string
     */
    public function version()
    {
        return '1.0';
    }

    /**
     * Get OAuth base protocol parameters.
     *
     * @return array
     */
    public function baseProtocolParameters()
    {
        return [
            'oauth_consumer_key' => $this->config()->consumerKey(),
            'oauth_nonce' => $this->nonce(),
            'oauth_signature_method' => $this->signer()->method(),
            'oauth_timestamp' => $this->timestamp(),
            'oauth_version' => $this->version(),
        ];
    }

    /**
     * Build authorization headers.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function authorizationHeaders(array $parameters)
    {
        $parameters = http_build_query($parameters, '', ', ', PHP_QUERY_RFC3986);

        return "OAuth $parameters";
    }
}
