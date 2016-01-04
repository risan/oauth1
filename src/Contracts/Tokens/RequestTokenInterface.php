<?php

namespace OAuth1\Contracts\Tokens;

use Psr\Http\Message\ResponseInterface;

interface RequestTokenInterface extends TokenInterface
{
    /**
     * Create from HTTP response.
     *
     * @param Psr\Http\Message\ResponseInterface $response
     *
     * @return OAuth1\Contracts\Tokens\RequestTokenInterface
     */
    public static function fromHttpResponse(ResponseInterface $response);
}
