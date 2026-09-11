<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParserInterface;

class UriConfig implements UriConfigInterface
{
    /**
     * The UriParserInterface implementation.
     *
     * @return UriParserInterface
     */
    protected UriParserInterface $parser;

    /**
     * The base URI.
     */
    protected ?UriInterface $base = null;

    /**
     * The URI for obtaining temporary credentials. Also known as request token
     * URI.
     */
    protected UriInterface $temporaryCredentials;

    /**
     * The URI for asking user to authorize the request.
     */
    protected UriInterface $authorization;

    /**
     * The URI for obtaining token credentials. Also known as access token
     * URI.
     */
    protected UriInterface $tokenCredentials;

    /**
     * The callback URI.
     */
    protected UriInterface $callback;

    /**
     * Create UriConfig instance.
     */
    public function __construct(array $uris, UriParserInterface $parser)
    {
        $this->parser = $parser;
        $this->setFromArray($uris);
    }

    /**
     * {@inheritdoc}
     */
    public function getParser(): UriParserInterface
    {
        return $this->parser;
    }

    /**
     * Set URIs from an array.
     *
     *
     * @return $this
     */
    public function setFromArray(array $uris): static
    {
        $this->validateUris($uris);

        $this->temporaryCredentials = $this->parser->toPsrUri($uris['temporary_credentials_uri']);
        $this->authorization = $this->parser->toPsrUri($uris['authorization_uri']);
        $this->tokenCredentials = $this->parser->toPsrUri($uris['token_credentials_uri']);

        if (isset($uris['base_uri'])) {
            $this->setBase($this->parser->toPsrUri($uris['base_uri']));
        }

        $this->callback = $this->parser->toPsrUri($uris['callback_uri']);

        return $this;
    }

    /**
     * Validate the given URI array.
     *
     *
     *
     * @throws InvalidArgumentException
     */
    public function validateUris(array $uris): bool
    {
        $requiredParams = [
            'temporary_credentials_uri',
            'authorization_uri',
            'token_credentials_uri',
            'callback_uri',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($uris[$param]) || (! $uris[$param] instanceof UriInterface && ! is_string($uris[$param])) || (string) $uris[$param] === '') {
                throw new InvalidArgumentException("Missing URI configuration: {$param}.");
            }
        }

        $callback = $this->parser->toPsrUri($uris['callback_uri']);
        if ((string) $callback !== 'oob' && ! $this->parser->isAbsolute($callback)) {
            throw new InvalidArgumentException('The callback URI must be absolute or the case-sensitive value "oob".');
        }

        foreach (['base_uri'] as $param) {
            if (isset($uris[$param]) && ! $uris[$param] instanceof UriInterface && (! is_string($uris[$param]) || $uris[$param] === '')) {
                throw new InvalidArgumentException("Invalid URI configuration: {$param}.");
            }
        }

        return true;
    }

    /**
     * Set the base URI.
     *
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setBase(UriInterface $uri): static
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
    public function base(): ?UriInterface
    {
        return $this->hasBase() ? $this->base : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBase(): bool
    {
        return $this->base !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function forTemporaryCredentials(): UriInterface
    {
        return $this->build($this->temporaryCredentials);
    }

    /**
     * {@inheritdoc}
     */
    public function forAuthorization(): UriInterface
    {
        return $this->build($this->authorization);
    }

    /**
     * {@inheritdoc}
     */
    public function forTokenCredentials(): UriInterface
    {
        return $this->build($this->tokenCredentials);
    }

    /**
     * {@inheritdoc}
     */
    public function callback(): UriInterface
    {
        if ((string) $this->callback === 'oob') {
            return $this->callback;
        }

        return $this->build($this->callback);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCallback(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function build(UriInterface|string $uri): UriInterface
    {
        $uri = $this->parser->toPsrUri($uri);

        if ($this->parser->isAbsolute($uri)) {
            return $uri;
        }

        $base = $this->base();
        if ($base === null) {
            throw new InvalidArgumentException('A base URI is required when using a relative URI.');
        }

        return $this->parser->resolve($base, $uri);
    }

    /**
     * Check if the given URI should be resolved to absolute URI.
     */
    public function shouldBeResolvedToAbsoluteUri(UriInterface $uri): bool
    {
        return ! $this->parser->isAbsolute($uri) && $this->hasBase();
    }
}
