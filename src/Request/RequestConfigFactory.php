<?php

namespace Risan\OAuth\Request;

use Risan\OAuth1\Config;
use Risan\OAuth1\Signature\HmacSha1Signer;

class RequestConfigFactory
{
    public static function create(array $configurations)
    {
        $config = Config::createFromArray($configurations);

        return new RequestConfig($config, new HmacSha1Signer, new NonceGenerator);
    }
}
