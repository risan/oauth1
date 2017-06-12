<?php

namespace Risan\OAuth1\Flows;

use InvalidArgumentException;
use Risan\OAuth1\Contracts\Tokens\AccessTokenInterface;

trait GrantedFlow
{
    /**
     * Granted access token.
     *
     * @var \OAuth1\Contracts\Tokens\AccessTokenInterface
     */
    protected $grantedAccessToken;

    /**
     * Get resource base url.
     *
     * @return string|null
     */
    public function resourceBaseUrl()
    {
        return $this->config()->resourceBaseUrl();
    }

    /**
     * Set resource base url.
     *
     * @param string $url
     *
     * @return \OAuth1\Contracts\GrantedFlowInterface
     */
    public function setResourceBaseUrl($url)
    {
        $this->config()->setResourceBaseUrl($url);

        return $this;
    }

    /**
     * Build resource url.
     *
     * @param string $url
     *
     * @return string
     */
    public function resourceUrl($url)
    {
        if (is_null($this->resourceBaseUrl())) {
            return $url;
        }

        return $this->resourceBaseUrl().$url;
    }

    /**
     * Send HTTP request to protected resource.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $url, $options = [])
    {
        if (!$this->grantedAccessToken() instanceof AccessTokenInterface) {
            throw new InvalidArgumentException('No access token has been set.');
        }

        $resourceUrl = $this->resourceUrl($url);

        $headers = [
            'headers' => $this->grantedRequestHeaders($this->grantedAccessToken(), $resourceUrl, $method, $options),
        ];

        return $this->httpClient()->request($method, $resourceUrl, array_merge($options, $headers));
    }

    /**
     * Send HTTP GET request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * Send POST DELETE request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, $options = [])
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * Send HTTP PUT request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url, $options = [])
    {
        return $this->request('PUT', $url, $options);
    }

    /**
     * Send HTTP PATCH request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function patch($url, $options = [])
    {
        return $this->request('PATCH', $url, $options);
    }

    /**
     * Send HTTP DELETE request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($url, $options = [])
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * Send HTTP HEAD request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function head($url, $options = [])
    {
        return $this->request('HEAD', $url, $options);
    }

    /**
     * Send HTTP OPTIONS request to protected resource.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function options($url, $options = [])
    {
        return $this->request('OPTIONS', $url, $options);
    }

    /**
     * Get granted access token.
     *
     * @return \OAuth1\Contracts\Tokens\AccessTokenInterface
     */
    public function grantedAccessToken()
    {
        return $this->grantedAccessToken;
    }

    /**
     * Set granted access token.
     *
     * @param \OAuth1\Contracts\Tokens\AccessTokenInterface $accessToken
     *
     * @return \OAuth1\Contracts\GrantedFlowInterface
     */
    public function setGrantedAccessToken(AccessTokenInterface $accessToken)
    {
        $this->grantedAccessToken = $accessToken;

        return $this;
    }

    /**
     * Get granted request headers.
     *
     * @param \OAuth1\Contracts\Tokens\AccessTokenInterface $accessToken
     * @param string                                        $url
     * @param string                                        $httpVerb
     * @param array                                         $options
     *
     * @return array
     */
    public function grantedRequestHeaders(AccessTokenInterface $accessToken, $url, $httpVerb, $options = [])
    {
        $parameters = $this->baseProtocolParameters();

        if (isset($options['query'])) {
            $parameters = array_merge($parameters, $options['query']);
        }

        $parameters['oauth_token'] = $accessToken->key();
        $parameters['oauth_signature'] = $this->signer()->setTokenSecret($accessToken->secret())->sign($url, $parameters, $httpVerb);

        return [
            'Authorization' => $this->authorizationHeaders($parameters),
        ];
    }

    /**
     * Get http client instance.
     *
     * @return \OAuth1\Contracts\HttpClientInterface
     */
    abstract public function httpClient();

    /**
     * Get client configuration.
     *
     * @return \OAuth1\Contracts\ConfigInterface
     */
    abstract public function config();

    /**
     * Get signer.
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    abstract public function signer();

    /**
     * Get OAuth base protocol parameters.
     *
     * @return array
     */
    abstract public function baseProtocolParameters();

    /**
     * Build authorization headers.
     *
     * @param array $parameters
     *
     * @return string
     */
    abstract public function authorizationHeaders(array $parameters);
}
