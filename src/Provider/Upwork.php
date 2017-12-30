<?php

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;

class Upwork implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig()
    {
        return [
            'temporary_credentials_uri' => 'https://www.upwork.com/api/auth/v1/oauth/token/request',
            'authorization_uri' => 'https://www.upwork.com/services/api/auth',
            'token_credentials_uri' => 'https://www.upwork.com/api/auth/v1/oauth/token/access',
            'base_uri' => 'https://www.upwork.com/',
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
