<?php

namespace OAuth1Client\Contracts\OAuth1Flows;

interface TemporaryCredentialsFlowInterface {
    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials();

    /**
     * Temporary credentials url.
     *
     * @return string
     */
    public function temporaryCredentialsUrl();

    /**
     * Temporary credentials header.
     *
     * @return array
     */
    public function temporaryCredentialsHeaders();
}
