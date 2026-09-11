<?php

declare(strict_types=1);

namespace Risan\OAuth1\Provider;

use Risan\OAuth1\Signature\SignerInterface;

interface ProviderInterface
{
    /**
     * Get provider's URI configuration.
     */
    public function getUriConfig(): array;

    /**
     * Get provider's signer instance.
     */
    public function getSigner(): SignerInterface;
}
