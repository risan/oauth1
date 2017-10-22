<?php

namespace Risan\OAuth1;

interface OAuth1FactoryInterface
{
    /**
     * Create the OAuth1Interface instance.
     *
     * @param  array  $config
     * @return \Risan\OAuth1\OAuth1Interface
     */
    public function create(array $config);
}
