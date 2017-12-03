<?php

namespace Risan\OAuth1\Request;

use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class AuthorizationHeader implements AuthorizationHeaderInterface
{
    /**
     * The ProtocolParameterInterface instance.
     *
     * @var \Risan\OAuth1\Request\ProtocolParameterInterface
     */
    protected $protocolParameter;

    /**
     * Create a new instance of AuthorizationHeader class.
     *
     * @param \Risan\OAuth1\Request\ProtocolParameterInterface $protocolParameter
     */
    public function __construct(ProtocolParameterInterface $protocolParameter)
    {
        $this->protocolParameter = $protocolParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolParameter()
    {
        return $this->protocolParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->protocolParameter->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function forTemporaryCredentials()
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTemporaryCredentials()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTokenCredentials($temporaryCredentials, $verificationCode)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, $httpMethod, $uri, array $requestOptions = [])
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forProtectedResource($tokenCredentials, $httpMethod, $uri, $requestOptions)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeProtocolParameters(array $parameters)
    {
        array_walk($parameters, function (&$value, $key) {
            $value = rawurlencode($key) . '="' . rawurlencode($value) . '"';
        });

        return 'OAuth ' . implode(', ', $parameters);
    }
}
