<?php

namespace Risan\OAuth1\Request;

use DateTime;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;

class RequestBuilder implements RequestBuilderInterface
{
    /**
     * The ConfigInterface instance.
     *
     * @var \Risan\OAuth1\ConfigInterface
     */
    protected $config;

    /**
     * The SignerInterface instance.
     *
     * @var \Risan\OAuth1\Signature\SignerInterface
     */
    protected $signer;

    /**
     * The NonceGeneratorInterface instance.
     *
     * @var \Risan\OAuth1\Request\NonceGeneratorInterface
     */
    protected $nonceGenerator;

    /**
     * Create RequestBuilder instance.
     *
     * @param \Risan\OAuth1\ConfigInterface $config
     * @param \Risan\OAuth1\Signature\SignerInterface|null $signer
     * @param \Risan\OAuth1\Request\NonceGeneratorInterface|null $nonceGenerator
     */
    public function __construct(ConfigInterface $config, SignerInterface $signer = null, NonceGeneratorInterface $nonceGenerator = null)
    {
        $this->config = $config;
        $this->signer = $signer ?: new HmacSha1Signer;
        $this->nonceGenerator = $nonceGenerator ?: new NonceGenerator;

        if ($this->signer instanceof KeyBasedSignerInterface) {
            $this->signer->setClientCredentials($config->getClientCredentials());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * {@inheritDoc}
     */
    public function getNonceGenerator()
    {
        return $this->nonceGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentTimestamp()
    {
        return (new DateTime)->getTimestamp();
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentialsUrl()
    {
        return $this->config->getTemporaryCredentialsUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentialsAuthorizationHeader()
    {
        $parameters = $this->getBaseProtocolParameters();

        if ($this->config->hasCallbackUri()) {
            $parameters['oauth_callback'] = $this->config->getCallbackUri();
        }

        $this->addSignatureParameter($parameters, $this->getTemporaryCredentialsUrl(), 'POST');

        return $this->normalizeProtocolParameters($parameters);
    }

    /**
     * Get base protocol parameters for the authorization header.
     *
     * @return array
     */
    public function getBaseProtocolParameters()
    {
        return [
            'oauth_consumer_key' => $this->config->getClientCredentialsIdentifier(),
            'oauth_nonce' => $this->nonceGenerator->generate(),
            'oauth_signature_method' => $this->signer->getMethod(),
            'oauth_timestamp' => "{$this->getCurrentTimestamp()}",
            'oauth_version' => '1.0',
        ];
    }

    /**
     * Add signature parameter to the given protocol parameters.
     *
     * @param array  &$parameters
     * @param string $uri
     * @param string $httpMethod
     */
    public function addSignatureParameter(array &$parameters, $uri, $httpMethod = 'POST')
    {
        $parameters['oauth_signature'] = $this->signer->sign($uri, $parameters, $httpMethod);

        return $parameters;
    }

    /**
     * Normalize protocol parameters to be used as authorization header.
     *
     * @param  array  $parameters
     * @return string
     */
    public function normalizeProtocolParameters(array $parameters)
    {
        array_walk($parameters, function (&$value, $key) {
            $value = rawurlencode($key) . '="' . rawurlencode($value) . '"';
        });

        return 'OAuth ' . implode(', ', $parameters);
    }
}
