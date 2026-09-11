<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Psr\Http\Message\UriInterface;
use RuntimeException;

final class RsaSha1Signer implements BaseStringSignerInterface, SignerInterface
{
    use CanBuildBaseString;

    private OpenSSLAsymmetricKey $privateKey;

    public function __construct(OpenSSLAsymmetricKey|string $privateKey, ?string $passphrase = null)
    {
        if ($privateKey instanceof OpenSSLAsymmetricKey) {
            $this->privateKey = $privateKey;

            return;
        }

        $resolved = openssl_pkey_get_private($privateKey, $passphrase ?? '');
        if ($resolved === false) {
            throw new InvalidArgumentException('The RSA private key is invalid or its passphrase is incorrect.');
        }

        $this->privateKey = $resolved;
    }

    public function getMethod(): string
    {
        return 'RSA-SHA1';
    }

    public function isKeyBased(): bool
    {
        return false;
    }

    public function sign(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string
    {
        $signature = '';
        if (! openssl_sign($this->buildBaseString($uri, $parameters, $httpMethod), $signature, $this->privateKey, OPENSSL_ALGO_SHA1)) {
            throw new RuntimeException('Unable to create the RSA-SHA1 signature.');
        }

        return base64_encode($signature);
    }
}
