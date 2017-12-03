<?php

namespace Risan\OAuth1\Request;

use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * The AuthorizationHeaderInterface instance.
     *
     * @var \Risan\OAuth1\Request\AuthorizationHeaderInterface
     */
    protected $authorizationHeader;

    /**
     * The UriParserInterface instance.
     *
     * @var \Risan\OAuth1\Request\UriParserInterface
     */
    protected $uriParser;

    /**
     * Create the new instance of RequestFactory class.
     *
     * @param \Risan\OAuth1\Request\AuthorizationHeaderInterface $authorizationHeader
     * @param \Risan\OAuth1\Request\UriParserInterface           $uriParser
     */
    public function __construct(AuthorizationHeaderInterface $authorizationHeader, UriParserInterface $uriParser)
    {
        $this->authorizationHeader = $authorizationHeader;
        $this->uriParser = $uriParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationHeader()
    {
        return $this->authorizationHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->authorizationHeader->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriParser()
    {
        return $this->uriParser;
    }

    /**
     * {@inheritdoc}
     */
    public function createForTemporaryCredentials()
    {
        return $this->create('POST', (string) $this->getConfig()->getTemporaryCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTemporaryCredentials(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials)
    {
        return $this->uriParser->appendQueryParameters(
            $this->getConfig()->getAuthorizationUri(),
            ['oauth_token' => $temporaryCredentials->getIdentifier()]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createForTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        return $this->create('POST', (string) $this->getConfig()->getTokenCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTokenCredentials($temporaryCredentials, $verificationCode),
            ],
            'form_params' => [
                'oauth_verifier' => $verificationCode,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createForProtectedResource(TokenCredentials $tokenCredentials, $method, $uri, array $options = [])
    {
        return $this->create($method, (string) $this->getConfig()->buildUri($uri), array_replace_recursive([
            'headers' => [
                'Authorization' => $this->authorizationHeader->forProtectedResource($tokenCredentials, $method, $uri, $options),
            ],
        ], $options));
    }

    /**
     * Create a new instance of Request class.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options [description]
     *
     * @return \Risan\OAuth1\Request\RequestInterface
     */
    public function create($method, $uri, array $options = [])
    {
        return new Request($method, $uri, $options);
    }
}
