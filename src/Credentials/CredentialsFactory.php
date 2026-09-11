<?php

declare(strict_types=1);

namespace Risan\OAuth1\Credentials;

use Psr\Http\Message\ResponseInterface;

class CredentialsFactory implements CredentialsFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createTemporaryCredentialsFromResponse(ResponseInterface $response): TemporaryCredentials
    {
        $parameters = $this->getParametersFromResponse($response);

        $missingParameterKey = $this->getMissingParameterKey($parameters, [
            'oauth_token',
            'oauth_token_secret',
            'oauth_callback_confirmed',
        ]);

        if ($missingParameterKey !== null) {
            throw new CredentialsException("Unable to parse temporary credentials response. Missing parameter: {$missingParameterKey}.");
        }

        if ($parameters['oauth_callback_confirmed'] !== 'true') {
            throw new CredentialsException('Unable to parse temporary credentials response. Callback URI is not valid.');
        }

        return new TemporaryCredentials($parameters['oauth_token'], $parameters['oauth_token_secret']);
    }

    /**
     * {@inheritdoc}
     */
    public function createTokenCredentialsFromResponse(ResponseInterface $response): TokenCredentials
    {
        $parameters = $this->getParametersFromResponse($response);

        $missingParameterKey = $this->getMissingParameterKey($parameters, [
            'oauth_token',
            'oauth_token_secret',
        ]);

        if ($missingParameterKey !== null) {
            throw new CredentialsException("Unable to parse token credentials response. Missing parameter: {$missingParameterKey}.");
        }

        return new TokenCredentials($parameters['oauth_token'], $parameters['oauth_token_secret']);
    }

    /**
     * Get parameters from response.
     */
    public function getParametersFromResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();

        $parameters = [];

        parse_str($contents, $parameters);

        return $parameters;
    }

    /**
     * Get missing parameter's key.
     */
    public function getMissingParameterKey(array $parameters, array $requiredKeys = []): ?string
    {
        foreach ($requiredKeys as $key) {
            if (! isset($parameters[$key]) || ! is_string($parameters[$key]) || $parameters[$key] === '') {
                return $key;
            }
        }

        return null;
    }
}
