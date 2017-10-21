<?php

namespace Risan\OAuth1\Request;

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
     * {@inheritDoc}
     */
    public function getProtocolParameter()
    {
        return $this->protocolParameter;
    }

    /**
     * {@inheritDoc}
     */
    public function forTemporaryCredentials()
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTemporaryCredentials()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTokenCredentials($temporaryCredentials, $verificationCode)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function normalizeProtocolParameters(array $parameters)
    {
        array_walk($parameters, function (&$value, $key) {
            $value = rawurlencode($key) . '="' . rawurlencode($value) . '"';
        });

        return 'OAuth ' . implode(', ', $parameters);
    }
}
