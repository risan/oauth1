<?php

namespace Risan\OAuth1\Config;

interface ConfigFactoryInterface
{
    /**
     * Create ConfigInterface instance from array.
     *
     * @param  array  $params
     * @return \Risan\OAuth1\Config\ConfigInterface
     */
    public function createFromArray(array $params);
}
