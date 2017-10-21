<?php

namespace Risan\OAuth1\Test\Unit\Request;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\AuthorizationHeader;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Request\ProtocolParameterInterface;

class AuthorizationHeaderTest extends TestCase
{
    private $protocolParameterStub;
    private $authorizationHeader;
    private $temporaryCredentialsStub;

    function setUp()
    {
        $this->protocolParameterStub = $this->createMock(ProtocolParameterInterface::class);
        $this->authorizationHeader = new AuthorizationHeader($this->protocolParameterStub);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
    }

    /** @test */
    function it_can_get_protocol_parameter()
    {
        $this->assertSame($this->protocolParameterStub, $this->authorizationHeader->getProtocolParameter());
    }

    /** @test */
    function it_can_get_config()
    {
        $this->protocolParameterStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $this->authorizationHeader->getConfig());
    }

    /** @test */
    function it_can_normalize_protocol_parameters()
    {
        $parameters = [
            'foo' => 'bar',
            'full name' => 'john doe',
        ];

        $this->assertEquals(
            'OAuth foo="bar", full%20name="john%20doe"',
            $this->authorizationHeader->normalizeProtocolParameters($parameters)
        );
    }

    /** @test */
    function it_can_build_for_temporary_credentials()
    {
        $this->protocolParameterStub
            ->expects($this->once())
            ->method('forTemporaryCredentials')
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(
            'OAuth foo="bar"',
            $this->authorizationHeader->forTemporaryCredentials()
        );
    }

    /** @test */
    function it_can_build_for_token_credentials()
    {
        $this->protocolParameterStub
            ->expects($this->once())
            ->method('forTokenCredentials')
            ->with($this->temporaryCredentialsStub, 'verification_code')
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(
            'OAuth foo="bar"',
            $this->authorizationHeader->forTokenCredentials($this->temporaryCredentialsStub, 'verification_code')
        );
    }
}
