<?php

namespace Risan\OAuth1;

use InvalidArgumentException;
use Risan\OAuth1\Provider\ProviderInterface;

class ProviderFactory
{
    /*
     * Create the new OAuth1Interface instance based on the provider config.
     *
     * @param \Risan\OAuth1\Provider\ProviderInterface $provider
     * @param array $config
     *
     * @return \Risan\OAuth1\OAuth1Interface
     */
    public static function create(ProviderInterface $provider, array $config)
    {
        return OAuth1Factory::create(
            array_merge($provider->getUriConfig(), $config),
            $provider->getSigner()
        );
    }

    /*
    * Dynamically handle the OAuth1Interface instance creation.
    *
    * @param string $name
    * @param array $arguments
    *
    * @return \Risan\OAuth1\OAuth1Interface
    * @throws \InvalidArgumentException
    */
    public static function __callStatic($name, array $arguments)
    {
        $providerClassName = '\\Risan\\OAuth1\\Provider\\' . ucfirst($name);

        if (! class_exists($providerClassName)) {
            throw new InvalidArgumentException("Class {$providerClassName} is not exists.");
        }

        if (! isset($arguments[0])) {
            throw new InvalidArgumentException("You need to pass the configuration array to ProviderFactory::{$name} method.");
        }

        if (! is_array($arguments[0])) {
            throw new InvalidArgumentException('The configuration parameter must be an array.');
        }

        return static::create(new $providerClassName(), $arguments[0]);
    }
}
