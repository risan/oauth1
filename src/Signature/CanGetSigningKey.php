<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ServerIssuedCredentials;

trait CanGetSigningKey
{
    /**
     * The ClientCredentials instance.
     *
     * @var \Risan\OAuth1\Credentials\ClientCredentials
     */
    protected $clientCredentials;

    /**
     * The ServerIssuedCredentials instance.
     *
     * @var \Risan\OAuth1\Credentials\ServerIssuedCredentials
     */
    protected $serverIssuedCredentials;

    /**
     * Get the key for signing.
     *
     * @return string
     */
    public function getKey()
    {
        $key = rawurlencode($this->clientCredentials->getSecret()) . '&';

        if ($this->serverIssuedCredentials instanceof ServerIssuedCredentials) {
            $key .= rawurlencode($this->serverIssuedCredentials->getSecret());
        }

        return $key;
    }

    /**
     * Get the ClientCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     */
    public function getClientCredentials()
    {
        return $this->clientCredentials;
    }

    /**
     * Set the ServerIssuedCredentials instance for signing.
     *
     * @param \Risan\OAuth1\Credentials\ServerIssuedCredentials $serverIssuedCredentials
     */
    public function setServerIssuedCredentials(ServerIssuedCredentials $serverIssuedCredentials)
    {
        $this->serverIssuedCredentials = $setServerIssuedCredentials;

        return $this;
    }

    /**
     * Get the ServerIssuedCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ServerIssuedCredentials
     */
    public function getServerIssuedCredentials()
    {
        return $this->serverIssuedCredentials;
    }
}
