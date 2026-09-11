<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

class AuthorizationHeader implements AuthorizationHeaderInterface
{
    /**
     * The ProtocolParameterInterface instance.
     */
    protected ProtocolParameterInterface $protocolParameter;

    /**
     * Create a new instance of AuthorizationHeader class.
     */
    public function __construct(ProtocolParameterInterface $protocolParameter)
    {
        $this->protocolParameter = $protocolParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolParameter(): ProtocolParameterInterface
    {
        return $this->protocolParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->protocolParameter->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function forTemporaryCredentials(): string
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTemporaryCredentials()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): string
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forTokenCredentials($temporaryCredentials, $verificationCode)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, string $httpMethod, UriInterface|string $uri, array $requestOptions = []): string
    {
        return $this->normalizeProtocolParameters(
            $this->protocolParameter->forProtectedResource($tokenCredentials, $httpMethod, $uri, $requestOptions)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeProtocolParameters(array $parameters): string
    {
        array_walk($parameters, function (&$value, $key) {
            $value = rawurlencode((string) $key).'="'.rawurlencode((string) $value).'"';
        });

        return 'OAuth '.implode(', ', $parameters);
    }
}
