<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Request\UriParser;

trait CanBuildBaseString
{
    /**
     * The BaseStringBuilder instance.
     *
     * @var \Risan\OAuth1\Signature\BaseStringBuilderInterface
     */
    protected $baseStringBuilder;

    /**
     * Build the signature base string.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array                                 $parameters
     * @param string                                $httpMethod
     *
     * @return string
     */
    public function buildBaseString($uri, array $parameters = [], $httpMethod = 'POST')
    {
        return $this->getBaseStringBuilder()->build($httpMethod, $uri, $parameters);
    }

    /**
     * Get the BaseStringBuilder instance.
     *
     * @return \Risan\OAuth1\Signature\BaseStringBuilderInterface
     */
    public function getBaseStringBuilder()
    {
        if ($this->baseStringBuilder instanceof BaseStringBuilderInterface) {
            return $this->baseStringBuilder;
        }

        return $this->baseStringBuilder = new BaseStringBuilder(new UriParser);
    }
}
