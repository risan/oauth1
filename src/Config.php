<?php

namespace Risan\OAuth1;

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
     * The callback URI.
     *
     * @var string|null
     */
    protected $callbackUri;

    /**
     * The URL for obtaining temporary credentials. Also known as request token
     * URL.
     *
     * @var string
     */
    protected $temporaryCredentialsUrl;

    /**
     * The URL for asking user to authorize the request.
     *
     * @var string
     */
    protected $authorizationUrl;

    /**
     * The URL for obtaining token credentials. Also known as access token URL.
     *
     * @var string
     */
    protected $tokenCredentialsUrl;

    /**
     * Create new instance of Config class.
     *
     * @param \Risan\OAuth1\Credentials\ClientCredentials $clientCredentials
     * @param string $temporaryCredentialsUrl
     * @param string $authorizationUrl
     * @param string $tokenCredentialsUrl
     * @param string|null $callbackUri
     */
    public function __construct(ClientCredentials $clientCredentials, $temporaryCredentialsUrl, $authorizationUrl, $tokenCredentialsUrl, $callbackUri = null)
    {
        $this->clientCredentials = $clientCredentials;
        $this->temporaryCredentialsUrl = $temporaryCredentialsUrl;
        $this->authorizationUrl = $authorizationUrl;
        $this->tokenCredentialsUrl = $tokenCredentialsUrl;
        $this->callbackUri = $callbackUri;
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
    public function hasCallbackUri()
    {
        return $this->getCallbackUri() !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentialsUrl()
    {
        return $this->temporaryCredentialsUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl()
    {
        return $this->authorizationUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCredentialsUrl()
    {
        return $this->tokenCredentialsUrl;
    }

    /**
     * {@inheritDoc}
     */
    public static function createFromArray(array $config)
    {
        $requiredParams = [
            'client_credentials_identifier',
            'client_credentials_secret',
            'temporary_credentials_url',
            'authorization_url',
            'token_credentials_url',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($config[$param])) {
                throw new InvalidArgumentException("Missing OAuth1 client configuration: {$param}.");
            }
        }

        $callbackUri = isset($config['callback_uri']) ? $config['callback_uri'] : null;

        return new static(
            new ClientCredentials($config['client_credentials_identifier'], $config['client_credentials_secret']),
            $config['temporary_credentials_url'],
            $config['authorization_url'],
            $config['token_credentials_url'],
            $callbackUri
        );
    }
}
