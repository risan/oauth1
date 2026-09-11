<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Provider;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Provider\ProviderInterface;
use Risan\OAuth1\Provider\Twitter;
use Risan\OAuth1\Signature\HmacSha1Signer;

class TwitterTest extends TestCase
{
    private $twitter;

    protected function setUp(): void
    {
        $this->twitter = new Twitter;
    }

    #[Test]
    public function it_implements_provider_interface()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->twitter);
    }

    #[Test]
    public function it_has_correct_uri_config()
    {
        $this->assertEquals([
            'temporary_credentials_uri' => 'https://api.x.com/oauth/request_token',
            'authorization_uri' => 'https://api.x.com/oauth/authorize',
            'token_credentials_uri' => 'https://api.x.com/oauth/access_token',
            'base_uri' => 'https://api.x.com/2/',
        ], $this->twitter->getUriConfig());
    }

    #[Test]
    public function it_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1Signer::class, $this->twitter->getSigner());
    }
}
