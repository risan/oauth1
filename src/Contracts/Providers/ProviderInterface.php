<?php

namespace Risan\OAuth1\Contracts\Providers;

use Risan\OAuth1\Contracts\OAuth1ClientInterface;

interface ProviderInterface extends OAuth1ClientInterface
{
    /**
     * Get default provider's configuration.
     *
     * @return array
     */
    public function defaultConfig();
}
