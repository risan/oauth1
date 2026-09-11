<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

interface ConfigFactoryInterface
{
    /**
     * Create ConfigInterface instance from array.
     *
     *
     *
     * @throws \InvalidArgumentException
     */
    public function createFromArray(array $config): ConfigInterface;
}
