<?php

namespace Risan\OAuth1\Config;

interface ConfigFactoryInterface
{
    /**
     * Create ConfigInterface instance from array.
     *
     * @param  array  $config
     * @return \Risan\OAuth1\Config\ConfigInterface
     * @throws \InvalidArgumentException
     */
    public function createFromArray(array $config);
}
