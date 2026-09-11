<?php

declare(strict_types=1);

namespace Risan\OAuth1\Credentials;

abstract class Credentials implements CredentialsInterface
{
    /**
     * The credentials identifier.
     */
    protected string $identifier;

    /**
     * The credentials shared-secret.
     */
    protected string $secret;

    /**
     * Creaate the new Crendentials class instance.
     */
    public function __construct(string $identifier, string $secret)
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(): string
    {
        return $this->secret;
    }
}
