<?php

namespace OAuth1\Providers;

use OAuth1\Contracts\Providers\Provider as ProviderContract;

class Twitter extends Provider implements ProviderContract
{
    /**
     * Get default provider's configuration.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'request_token_url' => 'https://api.twitter.com/oauth/request_token',
            'authorize_url' => 'https://api.twitter.com/oauth/authorize',
            'access_token_url' => 'https://api.twitter.com/oauth/access_token',
            'resource_base_url' => 'https://api.twitter.com/1.1/',
        ];
    }
}

