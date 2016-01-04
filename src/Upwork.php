<?php

namespace OAuth1;

use OAuth1\Contracts\OAuth1ClientInterface;

class Upwork extends OAuth1 implements OAuth1ClientInterface
{
    /**
     * Create a new instance of Upwork client class.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge($config, [
            'request_token_url' => 'https://www.upwork.com/api/auth/v1/oauth/token/request',
            'authorize_url' => 'https://www.upwork.com/services/api/auth',
            'access_token_url' => 'https://www.upwork.com/api/auth/v1/oauth/token/access',
            'resource_base_url' => 'https://www.upwork.com/',
        ]);

        parent::__construct($config, null);
    }
}
