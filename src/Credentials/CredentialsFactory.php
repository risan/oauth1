<?php

namespace Risan\OAuth1\Credentials;

use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class CredentialsFactory implements CredentialsFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createTemporaryCredentialsFromResponse(ResponseInterface $response)
    {
        $parameters = $this->getParametersFromResponse($response);

        $missingParameterKey = $this->getMissingParameterKey($parameters, [
            'oauth_token',
            'oauth_token_secret',
            'oauth_callback_confirmed',
        ]);

        if (null !== $missingParameterKey) {
            throw new CredentialsException("Unable to parse temporary credentials response. Missing parameter: {$missingParameterKey}.");
        }

        if ($parameters['oauth_callback_confirmed'] !== 'true') {
            throw new CredentialsException('Unable to parse temporary credentials response. Callback URI is not valid.');
        }

        return new TemporaryCredentials($parameters['oauth_token'], $parameters['oauth_token_secret']);
    }

    /**
     * {@inheritDoc}
     */
    public function createTokenCredentialsFromResponse(ResponseInterface $response)
    {
        $parameters = $this->getParametersFromResponse($response);

        $missingParameterKey = $this->getMissingParameterKey($parameters, [
            'oauth_token',
            'oauth_token_secret',
        ]);

        if (null !== $missingParameterKey) {
            throw new CredentialsException("Unable to parse token credentials response. Missing parameter: {$missingParameterKey}.");
        }

        return new TokenCredentials($parameters['oauth_token'], $parameters['oauth_token_secret']);
    }

    /**
     * Get parameters from response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    public function getParametersFromResponse(ResponseInterface $response)
    {
        $contents = $response->getBody()->getContents();

        parse_str($contents, $parameters);

        return $parameters;
    }

    /**
     * Get missing parameter's key.
     *
     * @param  array  $parameters
     * @param  array  $requiredKeys
     * @return string|null
     */
    public function getMissingParameterKey(array $parameters, array $requiredKeys = [])
    {
        foreach ($requiredKeys as $key) {
            if (! isset($parameters[$key])) {
                return $key;
            }
        }
    }
}
