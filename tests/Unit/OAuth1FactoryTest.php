<?php

namespace Risan\OAuth1\Test\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Factory;
use Risan\OAuth1\OAuth1Interface;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\PlainTextSigner;

class OAuth1FactoryTest extends TestCase
{
    private $config;
    private $plainTextSignerStub;

    function setUp()
    {
        $this->config = [
            'client_credentials_identifier' => 'client_id',
            'client_credentials_secret' => 'client_secret',
            'base_uri' => 'http://example.com',
            'temporary_credentials_uri' => '/request_token',
            'authorization_uri' => '/authorize',
            'token_credentials_uri' => '/access_token',
            'callback_uri' => 'http://johndoe.net',
        ];

        $this->plainTextSignerStub = $this->createMock(PlainTextSigner::class);
    }

    /** @test */
    function it_can_create_oauth1_instance()
    {
        $oauth1 = OAuth1Factory::create($this->config);

        $this->assertInstanceOf(OAuth1Interface::class, $oauth1);

        $this->assertInstanceOf(
            HmacSha1Signer::class, 
            $oauth1->getRequestFactory()->getAuthorizationHeader()->getProtocolParameter()->getSigner()
        );
    }

    
    /** @test */
    function it_accepts_custom_signer_parameter()
    {
        $oauth1 = OAuth1Factory::create($this->config, $this->plainTextSignerStub);

        $this->assertInstanceOf(OAuth1Interface::class, $oauth1);

        $this->assertInstanceOf(
            PlainTextSigner::class,
            $oauth1->getRequestFactory()->getAuthorizationHeader()->getProtocolParameter()->getSigner()
        );
    }


    /** @test */
    function it_throws_exception_if_signer_not_implements_signer_interface()
    {
        $this->expectException(InvalidArgumentException::class);

        OAuth1Factory::create($this->config, 'foo');
    }
}
