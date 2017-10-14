<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Request\UriParserInterface;

class BaseStringBuilder implements BaseStringBuilderInterface
{
    /**
     * The UriParserInterface instance.
     *
     * @var \Risan\OAuth1\Request\UriParserInterface
     */
    protected $uriParser;

    public function __construct(UriParserInterface $uriParser)
    {
        $this->uriParser = $uriParser;
    }

     /**
     * {@inheritDoc}
     */
    public function getUriParser()
    {
        return $this->uriParser;
    }

    /**
     * {@inheritDoc}
     */
    public function build($httpMethod, $uri, array $parameters = [])
    {
        $uri = $this->uriParser->toPsrUri($uri);

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
        $uri = $this->uriParser->toPsrUri($uri);

        return $this->uriParser->buildFromParts([
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
     * @return string
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
}
