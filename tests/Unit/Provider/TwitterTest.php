<?php

namespace Risan\OAuth1\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Provider\Twitter;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Provider\ProviderInterface;

class TwitterTest extends TestCase
{
    private $twitter;

    function setUp()
    {
        $this->twitter = new Twitter();
    }

    /** @test */
    function it_implements_provider_interface()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->twitter);
    }

    /** @test */
    function it_has_correct_uri_config()
    {
        $this->assertEquals([
            'temporary_credentials_uri' => 'https://api.twitter.com/oauth/request_token',
            'authorization_uri' => 'https://api.twitter.com/oauth/authorize',
            'token_credentials_uri' => 'https://api.twitter.com/oauth/access_token',
            'base_uri' => 'https://api.twitter.com/1.1/',
        ], $this->twitter->getUriConfig());
    }

    /** @test */
    function it_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1Signer::class, $this->twitter->getSigner());
    }
}
