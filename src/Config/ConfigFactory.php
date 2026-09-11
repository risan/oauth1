<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

use InvalidArgumentException;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\UriParser;

class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $config): ConfigInterface
    {
        $requiredParams = [
            'client_credentials_identifier',
            'client_credentials_secret',
            'temporary_credentials_uri',
            'authorization_uri',
            'token_credentials_uri',
            'callback_uri',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($config[$param])) {
                throw new InvalidArgumentException("Missing OAuth1 client configuration: {$param}.");
            }
        }

        if (! is_string($config['client_credentials_identifier']) || $config['client_credentials_identifier'] === '') {
            throw new InvalidArgumentException('The OAuth1 client credentials identifier must be a non-empty string.');
        }

        if (! is_string($config['client_credentials_secret'])) {
            throw new InvalidArgumentException('The OAuth1 client credentials secret must be a string.');
        }

        $clientCredentials = new ClientCredentials(
            $config['client_credentials_identifier'],
            $config['client_credentials_secret']
        );

        $uriConfig = new UriConfig($config, new UriParser);

        return new Config(
            $clientCredentials,
            $uriConfig,
            $this->httpMethod($config['temporary_credentials_method'] ?? 'POST', 'temporary_credentials_method'),
            $this->httpMethod($config['token_credentials_method'] ?? 'POST', 'token_credentials_method'),
        );
    }

    private function httpMethod(mixed $method, string $key): string
    {
        if (! is_string($method) || preg_match('/^[A-Za-z]+$/', $method) !== 1) {
            throw new InvalidArgumentException("Invalid OAuth1 HTTP method configuration: {$key}.");
        }

        return strtoupper($method);
    }
}
