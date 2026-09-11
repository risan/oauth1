<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParser;

trait CanBuildBaseString
{
    /**
     * The BaseStringBuilder instance.
     */
    protected ?BaseStringBuilderInterface $baseStringBuilder = null;

    /**
     * Build the signature base string.
     */
    public function buildBaseString(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string
    {
        return $this->getBaseStringBuilder()->build($httpMethod, $uri, $parameters);
    }

    /**
     * Get the BaseStringBuilder instance.
     */
    public function getBaseStringBuilder(): BaseStringBuilderInterface
    {
        if ($this->baseStringBuilder instanceof BaseStringBuilderInterface) {
            return $this->baseStringBuilder;
        }

        return $this->baseStringBuilder = new BaseStringBuilder(new UriParser);
    }
}
