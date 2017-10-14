<?php

namespace Risan\OAuth1\Request;

use DateTime;
use GuzzleHttp\Psr7\Uri;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class RequestConfig implements RequestConfigInterface
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
     * @param \Risan\OAuth1\Signature\SignerInterface $signer
     * @param \Risan\OAuth1\Request\NonceGeneratorInterface $nonceGenerator
     */
    public function __construct(ConfigInterface $config, SignerInterface $signer, NonceGeneratorInterface $nonceGenerator)
    {
        $this->config = $config;
        $this->signer = $signer;
        $this->nonceGenerator = $nonceGenerator;

        if ($this->signer->isKeyBased()) {
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

        $this->addSignatureParameter($parameters, $this->getTemporaryCredentialsUrl());

        return $this->normalizeProtocolParameters($parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function buildAuthorizationUrl(TemporaryCredentials $temporaryCredentials)
    {
        return $this->appendQueryParametersToUri($this->config->getAuthorizationUrl(), [
            'oauth_token' => $temporaryCredentials->getIdentifier(),
        ]);
    }

     /**
     * {@inheritDoc}
     */
    public function getTokenCredentialsUrl()
    {
        return $this->config->getTokenCredentialsUrl();
    }

     /**
     * {@inheritDoc}
     */
    public function getTokenCredentialsAuthorizationHeader(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        $parameters = $this->getBaseProtocolParameters();

        $parameters['oauth_token'] = $temporaryCredentials->getIdentifier();

        $this->addSignatureParameter(
            $parameters,
            $this->getTokenCredentialsUrl(),
            $temporaryCredentials,
            [
                'form_params' => [
                    'oauth_verifier' => $verificationCode,
                ],
            ]
        );

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
     * @param \Risan\OAuth1\Credentials\ServerIssuedCredentials|null $serverIssuedCredentials
     * @param array  $requestOptions
     * @param string $httpMethod
     */
    public function addSignatureParameter(array &$parameters, $uri, ServerIssuedCredentials $serverIssuedCredentials = null, array $requestOptions = [], $httpMethod = 'POST')
    {
        $requestParameters = $parameters;

        if ($this->requestOptionsHas($requestOptions, 'query')) {
            $requestParameters = array_merge($requestParameters, $requestOptions['query']);
        }

        if ($this->requestOptionsHas($requestOptions, 'form_params')) {
            $requestParameters = array_merge($requestParameters, $requestOptions['form_params']);
        }

        if ($this->signer->isKeyBased() && $serverIssuedCredentials instanceof ServerIssuedCredentials) {
            $this->signer->setServerIssuedCredentials($setServerIssuedCredentials);
        }

        $parameters['oauth_signature'] = $this->signer->sign($uri, $requestParameters, $httpMethod);

        return $parameters;
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

    /**
     * Append query parameters to URI.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @return string
     */
    public function appendQueryParametersToUri($uri, array $parameters)
    {
        $uri = new Uri($uri);

        parse_str($uri->getQuery(), $queryParameters);

        $mergedParameters = array_merge($queryParameters, $parameters);

        return (string) $uri->withQuery(http_build_query($mergedParameters));
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
