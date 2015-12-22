<?php

namespace OAuth1Client\Credentials;

use OAuth1Client\Contracts\Credentials\ClientCredentialsInterface;

class ClientCredentials extends Credentials implements ClientCredentialsInterface {
    /**
     * Client credentials callback uri.
     *
     * @return string
     */
    protected $callbackUri;

    /**
     * Create a new instance of ClientCredentials.
     *
     * @param string $identifier
     * @param string $secret
     * @param string $callbackUri
     */
    public function __construct($identifier, $secret, $callbackUri)
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
        $this->callbackUri = $callbackUri;
    }

    /**
     * Get client credentials callback uri.
     *
     * @return string
     */
    public function callbackUri()
    {
        return $this->callbackUri;
    }
}
