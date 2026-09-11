<?php

declare(strict_types=1);

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;

class Twitter implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig(): array
    {
        return [
            'temporary_credentials_uri' => 'https://api.x.com/oauth/request_token',
            'authorization_uri' => 'https://api.x.com/oauth/authorize',
            'token_credentials_uri' => 'https://api.x.com/oauth/access_token',
            'base_uri' => 'https://api.x.com/2/',
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
