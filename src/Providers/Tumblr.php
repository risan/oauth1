<?php

namespace OAuth1\Providers;

use OAuth1\Contracts\Providers\Provider as ProviderContract;

class Tumblr extends Provider implements ProviderContract
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
