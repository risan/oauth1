<?php

namespace Risan\OAuth1\Request;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use GuzzleHttp\Psr7\UriResolver;
use Psr\Http\Message\UriInterface;

class UriParser implements UriParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAbsolute(UriInterface $uri)
    {
        return Uri::isAbsolute($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function isMissingScheme(UriInterface $uri)
    {
        return '' === $uri->getScheme() && '' !== $uri->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function buildFromParts(array $parts)
    {
        return Uri::fromParts($parts);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(UriInterface $baseUri, UriInterface $uri)
    {
        return UriResolver::resolve($baseUri, $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function appendQueryParameters(UriInterface $uri, array $parameters = [])
    {
        parse_str($uri->getQuery(), $existedParameters);

        $mergedParameters = array_merge($existedParameters, $parameters);

        return $uri->withQuery(http_build_query($mergedParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function toPsrUri($uri)
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        } elseif (is_string($uri)) {
            return new Uri($uri);
        }

        throw new InvalidArgumentException('URI must be a string or an instance of \Psr\Http\Message\UriInterface.');
    }
}
