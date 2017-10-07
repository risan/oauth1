<?php

namespace Risan\OAuth1\Signature;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class BaseStringBuilder implements BaseStringBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function build($httpMethod, $uri, array $parameters = [])
    {
        $uri = $this->parseToPsrUri($uri);

        $components = [];

        $components[] = rawurlencode($this->buildMethodComponent($httpMethod));

        $components[] = rawurlencode($this->buildUriComponent($uri));

        parse_str($uri->getQuery(), $queryParameters);

        $components[] = rawurlencode($this->buildParametersComponent(array_merge($queryParameters, $parameters)));

        return implode('&', $components);
    }

    /**
     * {@inheritDoc}
     */
    public function buildMethodComponent($httpMethod)
    {
        return strtoupper($httpMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function buildUriComponent($uri)
    {
        $uri = $this->parseToPsrUri($uri);

        return Uri::fromParts([
            'scheme' => $uri->getScheme(),
            'host' => $uri->getHost(),
            'port' => $uri->getPort(),
            'path' => $uri->getPath(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildParametersComponent(array $parameters)
    {
        $parameters = $this->normalizeParameters($parameters);

        return $this->buildQueryString($parameters);
    }

    /**
     * Normalize the given request parameters.
     *
     * @param  array  $parameters
     * @return array
     */
    public function normalizeParameters(array $parameters)
    {
        $normalized = [];

        // [1] Encode both the keys and values.
        // Decode it frist, in case the given data is already encoded.
        foreach ($parameters as $key => $value) {
            $key = rawurlencode(rawurldecode($key));

            if (is_array($value)) {
                $normalized[$key] = $this->normalizeParameters($value);
            } else {
                $normalized[$key] = rawurlencode(rawurldecode($value));
            }
        }

        // [2] Sort by the encoded key.
        ksort($normalized);

        return $normalized;
    }

    /**
     * Build query string from the given parameters.
     *
     * @param  array  $parameters
     * @param  array  $initialQueryParameters
     * @param  string $previousKey
     * @return strin
     */
    public function buildQueryString(array $parameters, array $initialQueryParameters = [], $previousKey = null)
    {
        $queryParameters = $initialQueryParameters;

        foreach ($parameters as $key => $value) {
            if ($previousKey !== null) {
                $key = "{$previousKey}[{$key}]";
            }

            if (is_array($value)) {
                $queryParameters = $this->buildQueryString($value, $queryParameters, $key);
            } else {
                $queryParameters[] = "{$key}={$value}";
            }
        }

        return $previousKey !== null ? $queryParameters : implode('&', $queryParameters);
    }

    /**
     * Parse the given uri to the PSR URIInterface instance.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
     */
    public function parseToPsrUri($uri)
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        } elseif (is_string($uri)) {
            return new Uri($uri);
        }

        throw new InvalidArgumentException('URI must be a string or an instance of \Psr\Http\Message\UriInterface');
    }
}
