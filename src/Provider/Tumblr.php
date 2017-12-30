<?php

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;

class Tumblr implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig()
    {
        return [
            'temporary_credentials_uri' => 'https://www.tumblr.com/oauth/request_token',
            'authorization_uri' => 'https://www.tumblr.com/oauth/authorize',
            'token_credentials_uri' => 'https://www.tumblr.com/oauth/access_token',
            'base_uri' => 'https://api.tumblr.com/v2/',
        ];
    }

    /*
     * {@inheritdoc}
     */
    public function getSigner()
    {
        return new HmacSha1Signer();
    }
}
