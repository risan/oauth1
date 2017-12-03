<?php

namespace Risan\OAuth1\Credentials;

abstract class Credentials implements CredentialsInterface
{
    /**
     * The credentials identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * The credentials shared-secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Creaate the new Crendentials class instance.
     *
     * @param string $identifier
     * @param string $secret
     */
    public function __construct($identifier, $secret)
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret()
    {
        return $this->secret;
    }
}
