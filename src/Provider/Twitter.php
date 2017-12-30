<?php

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;

class Twitter implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig()
    {
        return [
            'temporary_credentials_uri' => 'https://api.twitter.com/oauth/request_token',
            'authorization_uri' => 'https://api.twitter.com/oauth/authorize',
            'token_credentials_uri' => 'https://api.twitter.com/oauth/access_token',
            'base_uri' => 'https://api.twitter.com/1.1/',
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
