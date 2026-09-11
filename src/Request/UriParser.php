<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Psr\Http\Message\UriInterface;

class UriParser implements UriParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAbsolute(UriInterface $uri): bool
    {
        return Uri::isAbsolute($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function isMissingScheme(UriInterface $uri): bool
    {
        return $uri->getScheme() === '' && $uri->getHost() !== '';
    }

    /**
     * {@inheritdoc}
     */
    public function buildFromParts(array $parts): UriInterface
    {
        return Uri::fromParts($parts);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(UriInterface $baseUri, UriInterface $uri): UriInterface
    {
        return UriResolver::resolve($baseUri, $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function appendQueryParameters(UriInterface $uri, array $parameters = []): UriInterface
    {
        $query = ParameterList::fromQueryString($uri->getQuery())
            ->merge(ParameterList::fromArray($parameters))
            ->toQueryString();

        return $uri->withQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function toPsrUri(UriInterface|string $uri): UriInterface
    {
        return $uri instanceof UriInterface ? $uri : new Uri($uri);
    }
}
