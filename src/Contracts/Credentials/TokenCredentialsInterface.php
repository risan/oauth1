<?php

namespace OAuth1Client\Contracts\Credentials;

use Psr\Http\Message\ResponseInterface;

interface TokenCredentialsInterface extends CredentialsInterface {
    /**
     * Create from HTTP response.
     *
     * @param  Psr\Http\Message\ResponseInterface $response
     * @return OAuth1Client\Contracts\Credentials
     */
    static public function fromHttpResponse(ResponseInterface $response);
}
