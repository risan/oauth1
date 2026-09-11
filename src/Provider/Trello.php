<?php

declare(strict_types=1);

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;

class Trello implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig(): array
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
    public function getSigner(): SignerInterface
    {
        return new HmacSha1Signer;
    }
}
