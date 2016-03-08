<?php

namespace OAuth1\Providers;

use OAuth1\Contracts\Providers\Provider as ProviderContract;

class Upwork extends Provider implements ProviderContract
{
    /**
     * Get default provider's configuration.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'request_token_url' => 'https://www.upwork.com/api/auth/v1/oauth/token/request',
            'authorize_url' => 'https://www.upwork.com/services/api/auth',
            'access_token_url' => 'https://www.upwork.com/api/auth/v1/oauth/token/access',
            'resource_base_url' => 'https://www.upwork.com/',
        ];
    }
}


