<?php

namespace Risan\OAuth1\Signature;

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
