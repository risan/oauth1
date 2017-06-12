<?php

namespace Risan\OAuth1\Providers;

use Risan\OAuth1\OAuth1;
use Risan\OAuth1\Contracts\Providers\ProviderInterface;

abstract class Provider extends OAuth1 implements ProviderInterface
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
