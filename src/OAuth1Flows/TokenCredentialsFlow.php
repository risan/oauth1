<?php

namespace OAuth1Client\OAuth1Flows;

use InvalidArgumentException;
use OAuth1Client\Credentials\TokenCredentials;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

trait TokenCredentialsFlow {
    /**
     * Token credentials.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $temporaryIdentifier
     * @param  string   $verifier
     * @return OAuth1Client\Contracts\Credentials\TokenCredentialsInterface
     */
    public function tokenCredentials(TemporaryCredentialsInterface $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
        if (! $this->isValidTemporaryCredentials($temporaryCredentials, $temporaryIdentifier)) {
            throw new InvalidArgumentException('The received temporary identifier does not match.');
        }

        $response = $this->httpClient()->post($this->tokenCredentialsUrl(), [
            'headers' => $this->tokenCredentialsHeaders($temporaryCredentials, $verifier)
        ]);

        return TokenCredentials::fromHttpResponse($response);
    }

    /**
     * Is valid temporary credentials?
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $temporaryIdentifier
     * @return boolean
     */
    public function isValidTemporaryCredentials(TemporaryCredentialsInterface $temporaryCredentials, $temporaryIdentifier)
    {
        return $temporaryCredentials->identifier() === $temporaryIdentifier;
    }

    /**
     * Token credentials header.
     *
     * @param  OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface $temporaryCredentials
     * @param  string   $verifier
     * @return array
     */
    public function tokenCredentialsHeaders(TemporaryCredentialsInterface $temporaryCredentials, $verifier)
    {
        $this->signature()->setCredentials($temporaryCredentials);

        $parameters = $this->baseProtocolParameters();

        $parameters['oauth_token'] = $temporaryCredentials->identifier();
        $parameters['oauth_verifier'] = $verifier;
        $parameters['oauth_signature'] = $this->signature()->sign($this->tokenCredentialsUrl(), $parameters);

        return [
            'Authorization' => $this->authorizationHeaders($parameters)
        ];
    }
}
