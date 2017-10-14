<?php

namespace Risan\OAuth1\Config;

use InvalidArgumentException;
use Risan\OAuth1\Credentials\ClientCredentials;

class Config implements ConfigInterface
{
    /**
     * The ClientCredentials instance.
     *
     * @var \Risan\OAuth1\Credentials\ClientCredentials
     */
    protected $clientCredentials;

    /**
     * The UriConfigInterface instance.
     *
     * @var \Risan\OAuth1\Config\UriConfigInterface
     */
    protected $uri;

    /**
     * Create new instance of Config class.
     *
     * @param \Risan\OAuth1\Credentials\ClientCredentials $clientCredentials
     * @param \Risan\OAuth1\Config\UriConfigInterface $uri
     */
    public function __construct(ClientCredentials $clientCredentials, UriConfigInterface $uri)
    {
        $this->clientCredentials = $clientCredentials;
        $this->uri = $uri;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientCredentials()
    {
        return $this->clientCredentials;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientCredentialsIdentifier()
    {
        return $this->getClientCredentials()->getIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function getClientCredentialsSecret()
    {
        return $this->getClientCredentials()->getSecret();
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentialsUri()
    {
        return $this->uri->forTemporaryCredentials();
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUri()
    {
        return $this->uri->forAuthorization();
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCredentialsUri()
    {
        return $this->uri->forTokenCredentials();
    }

    /**
     * {@inheritDoc}
     */
    public function hasCallbackUri()
    {
        return $this->uri->hasCallback();
    }

    /**
     * {@inheritDoc}
     */
    public function getCallbackUri()
    {
        return $this->uri->callback();
    }

    /**
     * {@inheritDoc}
     */
    public static function createFromArray(array $config)
    {
        $requiredParams = [
            'client_credentials_identifier',
            'client_credentials_secret',
            'temporary_credentials_uri',
            'authorization_uri',
            'token_credentials_uri',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($config[$param])) {
                throw new InvalidArgumentException("Missing OAuth1 client configuration: {$param}.");
            }
        }

        return new static(
            new ClientCredentials($config['client_credentials_identifier'], $config['client_credentials_secret']),
            new UriConfig($config)
        );
    }
}
