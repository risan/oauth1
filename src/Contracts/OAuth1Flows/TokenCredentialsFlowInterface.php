<?php

namespace OAuth1Client\Contracts\OAuth1Flows;

use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

interface TokenCredentialsFlowInterface {
    /**
     * Token credentials.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $temporaryIdentifier
     * @param  string   $verifier
     * @return OAuth1Client\Contracts\Credentials\TokenCredentialsInterface
     */
    public function tokenCredentials(TemporaryCredentialsInterface $temporaryCredentials, $temporaryIdentifier, $verifier);

    /**
     * Is valid temporary credentials?
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $temporaryIdentifier
     * @return boolean
     */
    public function isValidTemporaryCredentials(TemporaryCredentialsInterface $temporaryCredentials, $temporaryIdentifier);

    /**
     * Token credentials url.
     *
     * @return string
     */
    public function tokenCredentialsUrl();

    /**
     * Token credentials header.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $verifier
     * @return array
     */
    public function tokenCredentialsHeaders(TemporaryCredentialsInterface $temporaryCredentials, $verifier);
}
