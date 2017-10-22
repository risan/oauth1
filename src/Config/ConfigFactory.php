<?php

namespace Risan\OAuth1\Config;

use InvalidArgumentException;
use Risan\OAuth1\Config\UriConfig;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Credentials\ClientCredentials;

class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createFromArray(array $config)
    {
        $requiredParams = [
            'client_credentials_identifier',
            'client_credentials_secret',
            'temporary_credentials_uri',
            'authorization_uri',
            'token_credentials_uri',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($config[$param])) {
                throw new InvalidArgumentException("Missing OAuth1 client configuration: {$param}.");
            }
        }

        $clientCredentials = new ClientCredentials(
            $config['client_credentials_identifier'],
            $config['client_credentials_secret']
        );

        $uriConfig = new UriConfig($config, new UriParser);

        return new Config($clientCredentials, $uriConfig);
    }
}
