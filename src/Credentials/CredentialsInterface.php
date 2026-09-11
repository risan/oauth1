<?php

declare(strict_types=1);

namespace Risan\OAuth1\Credentials;

interface CredentialsInterface
{
    /**
     * Get the credentials identifier.
     */
    public function getIdentifier(): string;

    /**
     * Get the credentials shared-secret.
     */
    public function getSecret(): string;
}
