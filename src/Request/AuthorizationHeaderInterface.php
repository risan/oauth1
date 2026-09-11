<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

interface AuthorizationHeaderInterface
{
    /**
     * Get the ProtocolParameterInterface instance.
     */
    public function getProtocolParameter(): ProtocolParameterInterface;

    /**
     * Get the ConfigInterface instance.
     */
    public function getConfig(): ConfigInterface;

    /**
     * Get authorization header for obtaining temporary credentials.
     */
    public function forTemporaryCredentials(): string;

    /**
     * Get authorization header for obtaining token credentials.
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): string;

    /**
     * Get authorization header for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, string $httpMethod, UriInterface|string $uri, array $requestOptions = []): string;

    /**
     * Normalize protocol parameters to be used as HTTP authorization header.
     */
    public function normalizeProtocolParameters(array $parameters): string;
}
