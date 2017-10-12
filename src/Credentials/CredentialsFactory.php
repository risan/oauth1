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
        $contents = $response->getBody()->getContents();

        parse_str($contents, $responseParameters);

        $requiredParames = [
            'oauth_token',
            'oauth_secret',
            'oauth_callback_confirmed',
        ];

        foreach ($requiredParames as $param) {
            if (! isset($responseParameters[$param])) {
                throw new CredentialsException("Unable to parse temporary credentials response. Missing parameter: {$param}.");
            }
        }

        if ($responseParameters['oauth_callback_confirmed'] !== 'true') {
            throw new CredentialsException('Unable to parse temporary credentials response. Callback URI is not valid.');
        }

        return new TemporaryCredentials($responseParameters['oauth_token'], $responseParameters['oauth_secret']);
    }
}
