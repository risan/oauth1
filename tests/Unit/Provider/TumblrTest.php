<?php

namespace Risan\OAuth1\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Provider\Tumblr;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Provider\ProviderInterface;

class TumblrTest extends TestCase
{
    private $tumblr;

    function setUp()
    {
        $this->tumblr = new Tumblr();
    }

    /** @test */
    function it_implements_provider_interface()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->tumblr);
    }

    /** @test */
    function it_has_correct_uri_config()
    {
        $this->assertEquals([
            'temporary_credentials_uri' => 'https://www.tumblr.com/oauth/request_token',
            'authorization_uri' => 'https://www.tumblr.com/oauth/authorize',
            'token_credentials_uri' => 'https://www.tumblr.com/oauth/access_token',
            'base_uri' => 'https://api.tumblr.com/v2/',
        ], $this->tumblr->getUriConfig());
    }

    /** @test */
    function it_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1Signer::class, $this->tumblr->getSigner());
    }
}
