<?php

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;

class Trello implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig()
    {
        return [
            'temporary_credentials_uri' => 'https://trello.com/1/OAuthGetRequestToken',
            'authorization_uri' => 'https://trello.com/1/OAuthAuthorizeToken',
            'token_credentials_uri' => 'https://trello.com/1/OAuthGetAccessToken',
            'base_uri' => 'https://api.trello.com/1/',
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
