<?php

namespace OAuth1Client\Credentials;

use Psr\Http\Message\ResponseInterface;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

class TemporaryCredentials extends Credentials implements TemporaryCredentialsInterface {
    /**
     * Create from HTTP response.
     *
     * @param  Psr\Http\Message\ResponseInterface $response
     * @return OAuth1Client\Contracts\Credentials
     */
    static public function fromHttpResponse(ResponseInterface $response)
    {
        parse_str($response->getBody()->getContents(), $contents);

        return new static($contents['oauth_token'], $contents['oauth_token_secret']);
    }
}
