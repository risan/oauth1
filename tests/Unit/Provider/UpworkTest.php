<?php

namespace Risan\OAuth1\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Provider\Upwork;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Provider\ProviderInterface;

class UpworkTest extends TestCase
{
    private $upwork;

    function setUp()
    {
        $this->upwork = new Upwork();
    }

    /** @test */
    function it_implements_provider_interface()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->upwork);
    }

    /** @test */
    function it_has_correct_uri_config()
    {
        $this->assertEquals([
            'temporary_credentials_uri' => 'https://www.upwork.com/api/auth/v1/oauth/token/request',
            'authorization_uri' => 'https://www.upwork.com/services/api/auth',
            'token_credentials_uri' => 'https://www.upwork.com/api/auth/v1/oauth/token/access',
            'base_uri' => 'https://www.upwork.com/',
        ], $this->upwork->getUriConfig());
    }

    /** @test */
    function it_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1Signer::class, $this->upwork->getSigner());
    }
}
