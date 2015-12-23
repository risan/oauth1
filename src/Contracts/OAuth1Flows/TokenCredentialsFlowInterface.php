<?php

namespace OAuth1Client\Contracts\OAuth1Flows;

interface TokenCredentialsFlowInterface {
    /**
     * Token credentials.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $temporaryIdentifier
     * @param  string   $verifier
     * @return OAuth1Client\Contracts\Credentials\TokenCredentialsInterface                                              [description]
     */
    public function tokenCredentials(TemporaryCredentialsInterface $temporaryCredentials, $temporaryIdentifier, $verifier);

    /**
     * Token credentials url.
     *
     * @return string
     */
    public function tokenCredentialsUrl();

    /**
     * Token credentials header.
     *
     * @return array
     */
    public function tokenCredentialsHeaders();
}
