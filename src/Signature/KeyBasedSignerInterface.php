<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

interface KeyBasedSignerInterface
{
    /**
     * Get the key for signing.
     *
     * @return string
     */
    public function getKey();

    /**
     * Set the ClientCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     */
    public function setClientCredentials(ClientCredentials $clientCredentials);

    /**
     * Get the ClientCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     */
    public function getClientCredentials();

    /**
     * Set the ServerIssuedCredentials instance for signing.
     *
     * @param \Risan\OAuth1\Credentials\ServerIssuedCredentials $serverIssuedCredentials
     */
    public function setServerIssuedCredentials(ServerIssuedCredentials $serverIssuedCredentials);

    /**
     * Get the ServerIssuedCredentials instance for signing.
     *
     * @return \Risan\OAuth1\Credentials\ServerIssuedCredentials
     */
    public function getServerIssuedCredentials();
}
