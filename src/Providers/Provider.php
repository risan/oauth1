<?php

namespace OAuth1\Providers;

use OAuth1\OAuth1;
use OAuth1\Contracts\Providers\Provider as ProviderContract;

abstract class Provider extends OAuth1 implements ProviderContract
{
    /**
     * Create a new instance of Twitter client class.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge($config, $this->defaultConfig());

        parent::__construct($config, null);
    }

    /**
     * Get default provider's configuration.
     *
     * @return array
     */
    abstract public function defaultConfig();
}
