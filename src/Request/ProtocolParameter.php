<?php

namespace Risan\OAuth1\Request;

use DateTime;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class ProtocolParameter implements ProtocolParameterInterface
{
    /**
     * The ConfigInterface instance.
     *
     * @var \Risan\OAuth1\Config\ConfigInterface
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
     * Create ProtocolParameter instance.
     *
     * @param \Risan\OAuth1\ConfigInterface $config
     * @param \Risan\OAuth1\Signature\SignerInterface $signer
     * @param \Risan\OAuth1\Request\NonceGeneratorInterface $nonceGenerator
     */
    public function __construct(ConfigInterface $config, SignerInterface $signer, NonceGeneratorInterface $nonceGenerator)
    {
        $this->config = $config;
        $this->signer = $signer;
        $this->nonceGenerator = $nonceGenerator;
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
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * {@inheritDoc}
     */
    public function getBase()
    {
        return [
            'oauth_consumer_key' => $this->config->getClientCredentialsIdentifier(),
            'oauth_nonce' => $this->nonceGenerator->generate(),
            'oauth_signature_method' => $this->signer->getMethod(),
            'oauth_timestamp' => "{$this->getCurrentTimestamp()}",
            'oauth_version' => $this->getVersion(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function forTemporaryCredentials()
    {
        $parameters = $this->getBase();

        if ($this->config->hasCallbackUri()) {
            $parameters['oauth_callback'] = $this->config->getCallbackUri();
        }

        $parameters['oauth_signature'] = $this->getSignature($parameters, $this->config->getTemporaryCredentialsUri());

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        $parameters = $this->getBase();

        $parameters['oauth_token'] = $temporaryCredentials->getIdentifier();

        $requestOptions = [
            'form_params' => ['oauth_verifier' => $verificationCode],
        ];

        $parameters['oauth_signature'] = $this->getSignature(
            $parameters,
            $this->config->getTokenCredentialsUri(),
            $temporaryCredentials,
            $requestOptions
        );

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, $httpMethod, $uri, array $requestOptions = [])
    {
        $parameters = $this->getBase();

        $parameters['oauth_token'] = $tokenCredentials->getIdentifier();

        $parameters['oauth_signature'] = $this->getSignature(
            $parameters,
            $this->config->buildUri($uri),
            $tokenCredentials,
            $requestOptions,
            $httpMethod
        );

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getSignature(array $protocolParameters, $uri, ServerIssuedCredentials $serverIssuedCredentials = null, array $requestOptions = [], $httpMethod = 'POST')
    {
        $signatureParameters = $this->signatureParameters($protocolParameters, $requestOptions);

        return $this->setupSigner($serverIssuedCredentials)
            ->sign($uri, $signatureParameters, $httpMethod);
    }

    /**
     * Build the signature parameters to be signed.
     *
     * @param  array  $protocolParameters
     * @param  array  $requestOptions
     * @return array
     */
    public function signatureParameters(array $protocolParameters, array $requestOptions = [])
    {
        $parameters = $protocolParameters;

        if ($this->requestOptionsHas($requestOptions, 'query')) {
            $parameters = array_merge($parameters, $requestOptions['query']);
        }

        if ($this->requestOptionsHas($requestOptions, 'form_params')) {
            $parameters = array_merge($parameters, $requestOptions['form_params']);
        }

        return $parameters;
    }

    /**
     * Setup the signer.
     *
     * @param  \Risan\OAuth1\Credentials\ServerIssuedCredentials|null $serverIssuedCredentials
     * @return \Risan\OAuth1\Signature\SignerInterface
     */
    public function setupSigner(ServerIssuedCredentials $serverIssuedCredentials = null)
    {
        if ($this->shouldSignWithClientCredentials()) {
            $this->signer->setClientCredentials($this->config->getClientCredentials());
        }

        if ($this->shouldSignWithServerIssuedCredentials($serverIssuedCredentials)) {
            $this->signer->setServerIssuedCredentials($serverIssuedCredentials);
        }

        return $this->signer;
    }

    /**
     * Should sign with the client credentials.
     *
     * @return boolean
     */
    public function shouldSignWithClientCredentials()
    {
        return $this->signer->isKeyBased();
    }

    /**
     * Should sign with the server issued credentials.
     *
     * @param  \Risan\OAuth1\Credentials\ServerIssuedCredentials|null $serverIssuedCredentials
     * @return boolean
     */
    public function shouldSignWithServerIssuedCredentials(ServerIssuedCredentials $serverIssuedCredentials = null)
    {
        return $this->signer->isKeyBased() && $serverIssuedCredentials !== null;
    }

    /**
     * Check if request options has the given key option.
     *
     * @param  array  $requestOptions
     * @param  string $key
     * @return boolean
     */
    public function requestOptionsHas(array $requestOptions, $key)
    {
        return isset($requestOptions[$key]) &&
            is_array($requestOptions[$key]) &&
            count($requestOptions[$key]) > 0;
    }
}
