<?php

namespace Risan\OAuth1\Tokens;

use Risan\OAuth1\Contracts\Tokens\RequestTokenInterface;
use Psr\Http\Message\ResponseInterface;

class RequestToken extends Token implements RequestTokenInterface
{
    /**
     * Create from HTTP response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \OAuth1\Contracts\Tokens\RequestTokenInterface
     */
    public static function fromHttpResponse(ResponseInterface $response)
    {
        parse_str($response->getBody()->getContents(), $contents);

        return new static($contents['oauth_token'], $contents['oauth_token_secret']);
    }
}
