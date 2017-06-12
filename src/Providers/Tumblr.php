<?php

namespace Risan\OAuth1\Providers;

use Risan\OAuth1\Contracts\Providers\ProviderInterface;

class Tumblr extends Provider implements ProviderInterface
{
    /**
     * Get default provider's configuration.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'request_token_url' => 'https://www.tumblr.com/oauth/request_token',
            'authorize_url' => 'https://www.tumblr.com/oauth/authorize',
            'access_token_url' => 'https://www.tumblr.com/oauth/access_token',
            'resource_base_url' => 'https://api.tumblr.com/v2/',
        ];
    }
}
