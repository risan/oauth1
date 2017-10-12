<?php

namespace Risan\OAuth1\Credentials;

use Risan\OAuth1\Credentials\ClientCredentials;

class ClientCredentials extends Credentials implements CredentialsInterface
{
    /**
     * Create the ClientCredentials instance from response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     * @throws \Risan\OAuth1\Credentials\CredentialsException
     */
    public static function createFromResponse(ResponseInterface $response)
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

        return new static($responseParameters['oauth_token'], $responseParameters['oauth_secret']);
    }
}
