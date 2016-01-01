<?php

namespace OAuth1;

use OAuth1\Contracts\OAuth1ClientInterface;

class Twitter extends OAuth1 implements OAuth1ClientInterface {
    /**
     * Create a new instance of Twitter client class.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge($config, [
            'request_token_url' => 'https://api.twitter.com/oauth/request_token',
            'authorize_url' => 'https://api.twitter.com/oauth/authorize',
            'access_token_url' => 'https://api.twitter.com/oauth/access_token',
            'resource_base_url' => 'https://api.twitter.com/1.1/'
        ]);

        parent::__construct($config, null);
    }
}
