<?php

namespace Risan\OAuth1\Credentials;

interface CredentialsInterface
{
    /**
     * Get the credentials identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get the credentials shared-secret.
     *
     * @return string
     */
    public function getSecret();
}
