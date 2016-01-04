<?php

use Symfony\CS\Config\Config;
use Symfony\CS\FixerInterface;
use Symfony\CS\Finder\DefaultFinder;

$finder = DefaultFinder::create()
    ->in(__DIR__ . '/src');

return Config::create()
    ->finder($finder)
    ->level(FixerInterface::SYMFONY_LEVEL)
    ->setUsingCache(true);
