<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Credentials\ClientCredentials;

class Config implements ConfigInterface
{
    /**
     * The ClientCredentials instance.
     */
    protected ClientCredentials $clientCredentials;

    /**
     * The UriConfigInterface instance.
     */
    protected UriConfigInterface $uri;

    /**
     * Create new instance of Config class.
     */
    public function __construct(
        ClientCredentials $clientCredentials,
        UriConfigInterface $uri,
        protected string $temporaryCredentialsMethod = 'POST',
        protected string $tokenCredentialsMethod = 'POST',
    ) {
        $this->clientCredentials = $clientCredentials;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentials(): ClientCredentials
    {
        return $this->clientCredentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentialsIdentifier(): string
    {
        return $this->getClientCredentials()->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getClientCredentialsSecret(): string
    {
        return $this->getClientCredentials()->getSecret();
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): UriConfigInterface
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryCredentialsUri(): UriInterface
    {
        return $this->uri->forTemporaryCredentials();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(): UriInterface
    {
        return $this->uri->forAuthorization();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenCredentialsUri(): UriInterface
    {
        return $this->uri->forTokenCredentials();
    }

    public function getTemporaryCredentialsMethod(): string
    {
        return $this->temporaryCredentialsMethod;
    }

    public function getTokenCredentialsMethod(): string
    {
        return $this->tokenCredentialsMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallbackUri(): UriInterface
    {
        return $this->uri->callback();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCallbackUri(): bool
    {
        return $this->uri->hasCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function buildUri(UriInterface|string $uri): UriInterface
    {
        return $this->uri->build($uri);
    }
}
