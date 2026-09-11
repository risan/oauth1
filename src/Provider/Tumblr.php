<?php

declare(strict_types=1);

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;

class Tumblr implements ProviderInterface
{
    /*
     * {@inheritdoc}
     */
    public function getUriConfig(): array
    {
        return [
            'temporary_credentials_uri' => 'https://www.tumblr.com/oauth/request_token',
            'authorization_uri' => 'https://www.tumblr.com/oauth/authorize',
            'token_credentials_uri' => 'https://www.tumblr.com/oauth/access_token',
            'token_credentials_method' => 'GET',
            'base_uri' => 'https://api.tumblr.com/v2/',
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
