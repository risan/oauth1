<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use DateTime;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Signature\SignerInterface;

class ProtocolParameter implements ProtocolParameterInterface
{
    /**
     * The ConfigInterface instance.
     */
    protected ConfigInterface $config;

    /**
     * The SignerInterface instance.
     */
    protected SignerInterface $signer;

    /**
     * The NonceGeneratorInterface instance.
     */
    protected NonceGeneratorInterface $nonceGenerator;

    /**
     * Create ProtocolParameter instance.
     */
    public function __construct(ConfigInterface $config, SignerInterface $signer, NonceGeneratorInterface $nonceGenerator)
    {
        $this->config = $config;
        $this->signer = $signer;
        $this->nonceGenerator = $nonceGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getSigner(): SignerInterface
    {
        return $this->signer;
    }

    /**
     * {@inheritdoc}
     */
    public function getNonceGenerator(): NonceGeneratorInterface
    {
        return $this->nonceGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentTimestamp(): int
    {
        return (new DateTime)->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return '1.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getBase(): array
    {
        return [
            'oauth_consumer_key' => $this->config->getClientCredentialsIdentifier(),
            'oauth_nonce' => $this->nonceGenerator->generate(),
            'oauth_signature_method' => $this->signer->getMethod(),
            'oauth_timestamp' => "{$this->getCurrentTimestamp()}",
            'oauth_version' => $this->getVersion(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function forTemporaryCredentials(): array
    {
        $parameters = $this->getBase();

        $parameters['oauth_callback'] = (string) $this->config->getCallbackUri();

        $parameters['oauth_signature'] = $this->getSignature(
            $parameters,
            $this->config->getTemporaryCredentialsUri(),
            httpMethod: $this->config->getTemporaryCredentialsMethod(),
        );

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function forTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): array
    {
        $parameters = $this->getBase();

        $parameters['oauth_token'] = $temporaryCredentials->getIdentifier();

        $method = $this->config->getTokenCredentialsMethod();
        $requestOptions = $method === 'GET'
            ? ['query' => ['oauth_verifier' => $verificationCode]]
            : ['form_params' => ['oauth_verifier' => $verificationCode]];

        $parameters['oauth_signature'] = $this->getSignature(
            $parameters,
            $this->config->getTokenCredentialsUri(),
            $temporaryCredentials,
            $requestOptions,
            httpMethod: $method,
        );

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function forProtectedResource(TokenCredentials $tokenCredentials, string $httpMethod, UriInterface|string $uri, array $requestOptions = []): array
    {
        $parameters = $this->getBase();

        $parameters['oauth_token'] = $tokenCredentials->getIdentifier();

        $parameters['oauth_signature'] = $this->getSignature(
            $parameters,
            $this->config->buildUri($uri),
            $tokenCredentials,
            $requestOptions,
            $httpMethod
        );

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignature(array $protocolParameters, UriInterface|string $uri, ?ServerIssuedCredentials $serverIssuedCredentials = null, array $requestOptions = [], string $httpMethod = 'POST'): string
    {
        $signatureParameters = $this->signatureParameters($protocolParameters, $requestOptions);

        return $this->setupSigner($serverIssuedCredentials)
            ->sign($uri, $signatureParameters, $httpMethod);
    }

    /**
     * Build the signature parameters to be signed.
     */
    public function signatureParameters(array $protocolParameters, array $requestOptions = []): array
    {
        $parameters = ParameterList::fromArray($protocolParameters);

        if ($this->requestOptionsHas($requestOptions, 'query')) {
            $parameters = $parameters->merge($this->parametersFromOption($requestOptions['query'], 'query'));
        }

        if ($this->requestOptionsHas($requestOptions, 'form_params')) {
            $parameters = $parameters->merge($this->parametersFromOption($requestOptions['form_params'], 'form_params'));
        } elseif (isset($requestOptions['body']) && is_string($requestOptions['body']) && $this->hasFormUrlencodedContentType($requestOptions)) {
            $parameters = $parameters->merge(ParameterList::fromQueryString($requestOptions['body']));
        }

        return $parameters->pairs();
    }

    /**
     * Setup the signer.
     */
    public function setupSigner(?ServerIssuedCredentials $serverIssuedCredentials = null): SignerInterface
    {
        if ($this->shouldSignWithClientCredentials() && $this->signer instanceof KeyBasedSignerInterface) {
            $this->signer->setClientCredentials($this->config->getClientCredentials());
        }

        if ($serverIssuedCredentials !== null && $this->shouldSignWithServerIssuedCredentials($serverIssuedCredentials) && $this->signer instanceof KeyBasedSignerInterface) {
            $this->signer->setServerIssuedCredentials($serverIssuedCredentials);
        }

        return $this->signer;
    }

    /**
     * Should sign with the client credentials.
     */
    public function shouldSignWithClientCredentials(): bool
    {
        return $this->signer instanceof KeyBasedSignerInterface && $this->signer->isKeyBased();
    }

    /**
     * Should sign with the server issued credentials.
     */
    public function shouldSignWithServerIssuedCredentials(?ServerIssuedCredentials $serverIssuedCredentials = null): bool
    {
        return $this->signer instanceof KeyBasedSignerInterface && $this->signer->isKeyBased() && $serverIssuedCredentials !== null;
    }

    /**
     * Check if request options has the given key option.
     */
    public function requestOptionsHas(array $requestOptions, string $key): bool
    {
        return isset($requestOptions[$key]) && $requestOptions[$key] !== [] && $requestOptions[$key] !== '';
    }

    private function parametersFromOption(mixed $value, string $option): ParameterList
    {
        if (is_string($value)) {
            return ParameterList::fromQueryString($value);
        }

        if (is_array($value)) {
            return ParameterList::fromArray($value);
        }

        throw new \InvalidArgumentException("The {$option} OAuth request option must be an array or query string.");
    }

    private function hasFormUrlencodedContentType(array $requestOptions): bool
    {
        foreach (($requestOptions['headers'] ?? []) as $name => $value) {
            if (strcasecmp((string) $name, 'Content-Type') === 0) {
                foreach ((array) $value as $contentType) {
                    $mediaType = strtolower(trim(explode(';', (string) $contentType, 2)[0]));
                    if ($mediaType === 'application/x-www-form-urlencoded') {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
