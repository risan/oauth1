<?php

namespace OAuth1Client\Contracts\Credentials;

interface ClientCredentialsInterface extends CredentialsInterface {
    /**
     * Get client credentials callback uri.
     *
     * @return string
     */
    public function callbackUri();
}
