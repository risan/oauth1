<?php

namespace Risan\OAuth1\Config;

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
     * @param \Risan\OAuth1\Config\UriConfigInterface     $uri
     */
    public function __construct(ClientCredentials $clientCredentials, UriConfigInterface $uri)
    {
        $this->clientCredentials = $clientCredentials;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentials()
    {
        return $this->clientCredentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentialsIdentifier()
    {
        return $this->getClientCredentials()->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentialsSecret()
    {
        return $this->getClientCredentials()->getSecret();
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryCredentialsUri()
    {
        return $this->uri->forTemporaryCredentials();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri()
    {
        return $this->uri->forAuthorization();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenCredentialsUri()
    {
        return $this->uri->forTokenCredentials();
    }

    /**
     * {@inheritdoc}
     */
    public function getCallbackUri()
    {
        return $this->uri->callback();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCallbackUri()
    {
        return $this->uri->hasCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri($uri)
    {
        return $this->uri->build($uri);
    }
}
