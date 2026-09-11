<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Signature\SignerInterface;

interface ProtocolParameterInterface
{
    /**
     * Get the ConfigInterface instance.
     */
    public function getConfig(): ConfigInterface;

    /**
     * Get the SignerInterface instance.
     */
    public function getSigner(): SignerInterface;

    /**
     * Get the NonceGeneratorInterface instance.
     */
    public function getNonceGenerator(): NonceGeneratorInterface;

    /**
     * Get the current timestamp in seconds since Unix Epoch.
     */
    public function getCurrentTimestamp(): int;

    /**
     * Get the OAuth1 protocol version.
     */
    public function getVersion(): string;

    /**
     * Get the base protocol parameters.
     */
    public function getBase(): array;

    /**
     * Create the signature.
     */
    public function getSignature(array $protocolParameters, UriInterface|string $uri, ?ServerIssuedCredentials $serverIssuedCredentials = null, array $requestOptions = [], string $httpMethod = 'POST'): string;

    /**
     * Get protocol parameters for obtaining temporary credentials.
     */
    public function forTemporaryCredentials(): array;

    /**
     * Get protocol parameters for obtaining token credentials.
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): array;

    /**
     * Get protocol parameters for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, string $httpMethod, UriInterface|string $uri, array $requestOptions = []): array;
}
