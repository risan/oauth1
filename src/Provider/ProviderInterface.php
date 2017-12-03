<?php

namespace Risan\OAuth1\Provider;

interface ProviderInterface
{
    /**
     * Get provider's URI configuration.
     *
     * @return array
     */
    public function getUriConfig();

    /**
     * Get provider's signer instance.
     *
     * @return \Risan\OAuth1\Signature\SignerInterface
     */
    public function getSigner();
}
