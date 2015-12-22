<?php

namespace OAuth1Client\Contracts\Credentials;

interface CredentialsInterface {
    /**
     * Get credentials identifier.
     *
     * @return string
     */
    public function identifier();

    /**
     * Get credentials secret.
     *
     * @return string
     */
    public function secret();
}
