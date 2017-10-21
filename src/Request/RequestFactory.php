<?php

namespace Risan\OAuth1\Request;

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
     * Create the new instance of RequestFactory class.
     *
     * @param \Risan\OAuth1\Request\AuthorizationHeaderInterface $authorizationHeader
     */
    public function __construct(AuthorizationHeaderInterface $authorizationHeader)
    {
        $this->authorizationHeader = $authorizationHeader;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationHeader()
    {
        return $this->authorizationHeader;
    }

   /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->authorizationHeader->getConfig();
    }

    /**
     * {@inheritDoc}
     */
    public function forTemporaryCredentials()
    {
        return $this->create('POST', $this->getConfig()->getTemporaryCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTemporaryCredentials(),
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, $verificationCode)
    {
        return $this->create('POST', $this->getConfig()->getTokenCredentialsUri(), [
            'headers' => [
                'Authorization' => $this->authorizationHeader->forTokenCredentials($temporaryCredentials, $verificationCode),
            ],
            'form_params' => [
                'oauth_verifier' => $verificationCode,
            ],
        ]);
    }

    /**
     * Create a new instance of Request class.
     *
     * @param  string $method
     * @param  strin $uri
     * @param  array  $options [description]
     * @return \Risan\OAuth1\Request\RequestInterface
     */
    public function create($method, $uri, array $options = [])
    {
        return new Request($method, $uri, $options);
    }
}
