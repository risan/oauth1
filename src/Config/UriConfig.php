<?php

namespace Risan\OAuth1\Config;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParserInterface;

class UriConfig implements UriConfigInterface
{
    /**
     * The UriParserInterface implementation.
     *
     * @return \Risan\OAuth1\Request\UriParserInterface
     */
    protected $parser;

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
     * @param array                                    $uris
     * @param \Risan\OAuth1\Request\UriParserInterface $parser
     */
    public function __construct(array $uris, UriParserInterface $parser)
    {
        $this->parser = $parser;
        $this->setFromArray($uris);
    }

    /**
     * {@inheritdoc}
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set URIs from an array.
     *
     * @param array $uris
     *
     * @return \Risan\OAuth1\Config\UriConfig
     */
    public function setFromArray(array $uris)
    {
        $this->validateUris($uris);

        $this->temporaryCredentials = $this->parser->toPsrUri($uris['temporary_credentials_uri']);
        $this->authorization = $this->parser->toPsrUri($uris['authorization_uri']);
        $this->tokenCredentials = $this->parser->toPsrUri($uris['token_credentials_uri']);

        if (isset($uris['base_uri'])) {
            $this->setBase($this->parser->toPsrUri($uris['base_uri']));
        }

        if (isset($uris['callback_uri'])) {
            $this->callback = $this->parser->toPsrUri($uris['callback_uri']);
        }

        return $this;
    }

    /**
     * Validate the given URI array.
     *
     * @param array $uris
     *
     * @return bool
     *
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
     * @param \Psr\Http\Message\UriInterface $uri
     *
     * @return \Risan\OAuth1\Config\UriConfig
     *
     * @throws \InvalidArgumentException
     */
    public function setBase(UriInterface $uri)
    {
        if (! $this->parser->isAbsolute($uri)) {
            throw new InvalidArgumentException('The base URI must be absolute.');
        }

        $this->base = $uri;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function base()
    {
        return $this->hasBase() ? $this->base : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBase()
    {
        return null !== $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function forTemporaryCredentials()
    {
        return $this->build($this->temporaryCredentials);
    }

    /**
     * {@inheritdoc}
     */
    public function forAuthorization()
    {
        return $this->build($this->authorization);
    }

    /**
     * {@inheritdoc}
     */
    public function forTokenCredentials()
    {
        return $this->build($this->tokenCredentials);
    }

    /**
     * {@inheritdoc}
     */
    public function callback()
    {
        return $this->hasCallback() ? $this->build($this->callback) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCallback()
    {
        return null !== $this->callback;
    }

    /**
     * {@inheritdoc}
     */
    public function build($uri)
    {
        $uri = $this->parser->toPsrUri($uri);

        if ($this->shouldBeResolvedToAbsoluteUri($uri)) {
            $uri = $this->parser->resolve($this->base(), $uri);
        }

        return $this->parser->isMissingScheme($uri) ? $uri->withScheme('http') : $uri;
    }

    /**
     * Check if the given URI should be resolved to absolute URI.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     *
     * @return bool
     */
    public function shouldBeResolvedToAbsoluteUri(UriInterface $uri)
    {
        return ! $this->parser->isAbsolute($uri) && $this->hasBase();
    }
}
