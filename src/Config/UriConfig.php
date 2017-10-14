<?php

namespace Risan\OAuth1\Config;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use GuzzleHttp\Psr7\UriResolver;

class UriConfig implements UriConfigInterface
{
    /**
     * The base URI.
     *
     * @var \Psr\Http\Message\UriInterface|null
     */
    protected $base;

    /**
     * The URI for obtaining temporary credentials. Also known as request token
     * URI.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $temporaryCredentials;

    /**
     * The URI for asking user to authorize the request.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $authorization;

    /**
     * The URI for obtaining token credentials. Also known as access token
     * URI.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $tokenCredentials;

    /**
     * The callback URI.
     *
     * @var \Psr\Http\Message\UriInterface|null
     */
    protected $callback;

    /**
     * Create UriConfig instance.
     *
     * @param array $uris
     */
    public function __construct(array $uris)
    {
        $this->setFromArray($uris);
    }

    /**
     * Set URIs from an array.
     *
     * @param array $uris
     * @return \Risan\OAuth1\Config\UriConfig
     */
    public function setFromArray(array $uris)
    {
        $this->validateUris($uris);

        $this->temporaryCredentials = $this->toPsrUri($uris['temporary_credentials_uri']);
        $this->authorization = $this->toPsrUri($uris['authorization_uri']);
        $this->tokenCredentials = $this->toPsrUri($uris['token_credentials_uri']);

        if (isset($uris['base_uri'])) {
            $this->setBase($this->toPsrUri($uris['base_uri']));
        }

        if (isset($uris['callback'])) {
            $this->callback = $this->toPsrUri($uris['callback_uri']);
        }

        return $this;
    }

    /**
     * Validate the given URI array.
     *
     * @param  array  $uris
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function validateUris(array $uris)
    {
        $requiredParams = [
            'temporary_credentials_uri',
            'authorization_uri',
            'token_credentials_uri',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($uris[$param])) {
                throw new InvalidArgumentException("Missing URI configuration: {$param}.");
            }
        }

        return true;
    }

    /**
     * Set the base URI.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return \Risan\OAuth1\Config\UriConfig
     * @throws \InvalidArgumentException
     */
    public function setBase(UriInterface $uri)
    {
        if (! $this->isAbsolute($uri)) {
            throw new InvalidArgumentException('The base URI must be absolute.');
        }

        $this->base = $uri;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function base()
    {
        return $this->hasBase() ? $this->base : null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasBase()
    {
        return $this->base !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function temporaryCredentials()
    {
        return $this->build($this->temporaryCredentials);
    }

    /**
     * {@inheritDoc}
     */
    public function authorization()
    {
        return $this->build($this->authorization);
    }

    /**
     * {@inheritDoc}
     */
    public function tokenCredentials()
    {
        return $this->build($this->tokenCredentials);
    }

    /**
     * {@inheritDoc}
     */
    public function callback()
    {
        return $this->hasCallback() ? $this->build($this->callback) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasCallback()
    {
        return $this->callback !== null;
    }

    /**
     * Parse and build the given URI.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    public function build($uri)
    {
        $uri = $this->toPsrUri($uri);

        if ($this->shouldBeResolvedToAbsoluteUri($uri)) {
            $uri = UriResolver::resolve($this->base(), $uri);
        }

        return $this->isSchemeMissing() ? $uri->withScheme('http') : $uri;
    }

    /**
     * Check if the given URI should be resolved to absolute URI.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return boolean
     */
    public function shouldBeResolvedToAbsoluteUri(UriInterface $uri)
    {
        return ! $this->isAbsolute($uri) && $this->hasBase();
    }

    /**
     * Check if the given URI missing the scheme path.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return boolean
     */
    public function isSchemeMissing(UriInterface $uri)
    {
        return $uri->getScheme() === '' && $uri->getHost() !== '';
    }

    /**
     * Check whether the given URI is absolute.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return boolean
     */
    public function isAbsolute(UriInterface $uri)
    {
        return Uri::isAbsolute($uri);
    }

    /**
     * Parse the given uri to the PSR URIInterface instance.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
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
