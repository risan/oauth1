<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Provider;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Provider\ProviderInterface;
use Risan\OAuth1\Provider\Trello;
use Risan\OAuth1\Signature\HmacSha1Signer;

class TrelloTest extends TestCase
{
    private $trello;

    protected function setUp(): void
    {
        $this->trello = new Trello;
    }

    #[Test]
    public function it_implements_provider_interface()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->trello);
    }

    #[Test]
    public function it_has_correct_uri_config()
    {
        $this->assertEquals([
            'temporary_credentials_uri' => 'https://trello.com/1/OAuthGetRequestToken',
            'authorization_uri' => 'https://trello.com/1/OAuthAuthorizeToken',
            'token_credentials_uri' => 'https://trello.com/1/OAuthGetAccessToken',
            'base_uri' => 'https://api.trello.com/1/',
        ], $this->trello->getUriConfig());
    }

    #[Test]
    public function it_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1Signer::class, $this->trello->getSigner());
    }
}
