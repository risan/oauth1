<?php

namespace OAuth1Client\OAuth1Flows;

use OAuth1Client\Credentials\TemporaryCredentials;

trait TemporaryCredentialsFlow {
    /**
     * Get temporary credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface
     */
    public function temporaryCredentials()
    {
        $response = $this->httpClient()->post($this->temporaryCredentialsUrl(), [
            'headers' => $this->temporaryCredentialsHeaders()
        ]);

        return TemporaryCredentials::fromHttpResponse($response);
    }

    /**
     * Temporary credentials header.
     *
     * @return array
     */
    public function temporaryCredentialsHeaders()
    {
        $parameters = $this->baseProtocolParameters();

        $parameters['oauth_signature'] = $this->signature()->sign($this->temporaryCredentialsUrl(), $parameters);

        return [
            'Authorization' => $this->authorizationHeaders($parameters)
        ];
    }
}
