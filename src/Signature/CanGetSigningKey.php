<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ClientCredentials;
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
        $key = '';

        if ($this->clientCredentials instanceof ClientCredentials) {
            $key .= rawurlencode($this->clientCredentials->getSecret());
        }

        // Keep the ampersand even if both keys are empty.
        $key .= '&';

        if ($this->serverIssuedCredentials instanceof ServerIssuedCredentials) {
            $key .= rawurlencode($this->serverIssuedCredentials->getSecret());
        }

        return $key;
    }

    /**
     * Set the ClientCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     */
    public function setClientCredentials(ClientCredentials $clientCredentials)
    {
        $this->clientCredentials = $clientCredentials;

        return $this;
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
        $this->serverIssuedCredentials = $serverIssuedCredentials;

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
