<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\ParameterList;
use Risan\OAuth1\Request\UriParserInterface;

class BaseStringBuilder implements BaseStringBuilderInterface
{
    public function __construct(protected UriParserInterface $uriParser)
    {
    }

    public function getUriParser(): UriParserInterface
    {
        return $this->uriParser;
    }

    public function build(string $httpMethod, UriInterface|string $uri, array $parameters = []): string
    {
        $uri = $this->uriParser->toPsrUri($uri);
        $all = ParameterList::fromQueryString($uri->getQuery())->merge(ParameterList::fromArray($parameters));

        return implode('&', [
            rawurlencode($this->buildMethodComponent($httpMethod)),
            rawurlencode($this->buildUriComponent($uri)),
            rawurlencode($this->buildParametersComponent($all->pairs())),
        ]);
    }

    public function buildMethodComponent(string $httpMethod): string
    {
        return strtoupper($httpMethod);
    }

    public function buildUriComponent(UriInterface|string $uri): string
    {
        $uri = $this->uriParser->toPsrUri($uri);
        $scheme = strtolower($uri->getScheme());
        $authority = strtolower($uri->getHost());
        $port = $uri->getPort();

        if ($port !== null && ! (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443))) {
            $authority .= ':'.$port;
        }

        return $scheme.'://'.$authority.($uri->getPath() === '' ? '/' : $uri->getPath());
    }

    public function buildParametersComponent(array $parameters): string
    {
        return $this->buildQueryString($this->normalizeParameters($parameters));
    }

    /** @return list<array{0: string, 1: string}> */
    public function normalizeParameters(array $parameters): array
    {
        return ParameterList::fromArray($parameters)->normalizedPairs();
    }

    public function buildQueryString(array $parameters): string
    {
        $pairs = ParameterList::fromArray($parameters)->pairs();

        return implode('&', array_map(static fn (array $pair): string => $pair[0].'='.$pair[1], $pairs));
    }
}
