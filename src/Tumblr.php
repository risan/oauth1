<?php

namespace OAuth1;

use OAuth1\Contracts\OAuth1ClientInterface;

class Tumblr extends OAuth1 implements OAuth1ClientInterface
{
    /**
     * Create a new instance of Tumblr client class.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge($config, [
            'request_token_url' => 'https://www.tumblr.com/oauth/request_token',
            'authorize_url' => 'https://www.tumblr.com/oauth/authorize',
            'access_token_url' => 'https://www.tumblr.com/oauth/access_token',
            'resource_base_url' => 'https://api.tumblr.com/v2/',
        ]);

        parent::__construct($config, null);
    }
}
