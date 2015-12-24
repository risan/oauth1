<?php

namespace OAuth1\Contracts\Flows;

use OAuth1\Contracts\Tokens\AccessTokenInterface;

interface GrantedFlowInterface {
    /**
     * Get resource base url.
     *
     * @return string|null
     */
    public function resourceBaseUrl();

    /**
     * Set resource base url.
     *
     * @param  string $url
     * @return OAuth1\Contracts\GrantedFlowInterface
     */
    public function setResourceBaseUrl($url);

    /**
     * Build resource url.
     *
     * @param  string $url
     * @return string
     */
    public function resourceUrl($url);

    /**
     * Send HTTP request to protected resource.
     *
     * @param  string $method
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request($method, $url, $options = []);

    /**
     * Send HTTP GET request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function get($url, $options = []);

    /**
     * Send POST DELETE request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function post($url, $options = []);

    /**
     * Send HTTP PUT request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function put($url, $options = []);

    /**
     * Send HTTP PATCH request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function patch($url, $options = []);

    /**
     * Send HTTP DELETE request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function delete($url, $options = []);

    /**
     * Send HTTP HEAD request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function head($url, $options = []);

    /**
     * Send HTTP OPTIONS request to protected resource.
     *
     * @param  string $url
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function options($url, $options = []);

    /**
     * Get granted access token.
     *
     * @return OAuth1\Contracts\Tokens\AccessTokenInterface
     */
    public function grantedAccessToken();

    /**
     * Set granted access token.
     *
     * @param $accessToken OAuth1\Contracts\Tokens\AccessTokenInterface
     * @return OAuth1\Contracts\GrantedFlowInterface
     */
    public function setGrantedAccessToken(AccessTokenInterface $accessToken);

    /**
     * Get granted request headers.
     *
     * @return array
     */
    public function grantedRequestHeaders();
}
