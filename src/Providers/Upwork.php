<?php

namespace Risan\OAuth1\Providers;

use Risan\OAuth1\Contracts\Providers\ProviderInterface;

class Upwork extends Provider implements ProviderInterface
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
