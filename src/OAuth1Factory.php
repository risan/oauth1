<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Request\RequestConfigFactory;
use Risan\OAuth1\Credentials\CredentialsFactory;

class OAuth1Factory
{
    /**
     * Create a new OAuth instance.
     *
     * @param  array  $config
     * @return \Risan\OAuth1\OAuth1
     */
    public static function create(array $config)
    {
        $requestConfig = RequestConfigFactory::create($config);

        return new OAuth1($requestConfig, new HttpClient, new CredentialsFactory);
    }
}
