<?php

namespace OAuth1\Flows;

use OAuth1\Tokens\RequestToken;

trait RequestTokenFlow
{
    /**
     * Request token url.
     *
     * @return string
     */
    public function requestTokenUrl()
    {
        return $this->config()->requestTokenUrl();
    }

     /**
      * Get request token.
      *
      * @return OAuth1\Contracts\Tokens\RequestTokenInterface
      */
     public function requestToken()
     {
         $response = $this->httpClient()->post($this->requestTokenUrl(), [
            'headers' => $this->requestTokenHeaders(),
        ]);

         return RequestToken::fromHttpResponse($response);
     }

    /**
     * Get request token headers.
     *
     * @return array
     */
    public function requestTokenHeaders()
    {
        $parameters = $this->baseProtocolParameters();

        $parameters['oauth_signature'] = $this->signer()->sign($this->requestTokenUrl(), $parameters);

        return [
            'Authorization' => $this->authorizationHeaders($parameters),
        ];
    }
}
