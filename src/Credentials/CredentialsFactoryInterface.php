<?php

namespace Risan\OAuth1\Credentials;

use Psr\Http\Message\ResponseInterface;

interface CredentialsFactoryInterface
{
    /**
     * Create TemporaryCredentials instance from response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Risan\OAuth1\Credentials\TemporaryCredentials
     * @throws \Risan\OAuth1\Credentials\CredentialsException
     */
    public function createTemporaryCredentialsFromResponse(ResponseInterface $response);

    /**
     * Create TokenCredentials instance from response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Risan\OAuth1\Credentials\TokenCredentials
     * @throws \Risan\OAuth1\Credentials\CredentialsException
     */
    public function createTokenCredentialsFromResponse(ResponseInterface $response);
}
