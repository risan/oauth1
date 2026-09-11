<?php

declare(strict_types=1);

namespace Risan\OAuth1\Credentials;

use Psr\Http\Message\ResponseInterface;

interface CredentialsFactoryInterface
{
    /**
     * Create TemporaryCredentials instance from response.
     *
     *
     *
     * @throws CredentialsException
     */
    public function createTemporaryCredentialsFromResponse(ResponseInterface $response): TemporaryCredentials;

    /**
     * Create TokenCredentials instance from response.
     *
     *
     *
     * @throws CredentialsException
     */
    public function createTokenCredentialsFromResponse(ResponseInterface $response): TokenCredentials;
}
