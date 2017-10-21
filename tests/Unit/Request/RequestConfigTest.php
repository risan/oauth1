<?php

namespace Risan\OAuth1\Test\Unit\Request;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\RequestConfig;
use Risan\OAuth1\Request\RequestConfigInterface;

class RequestConfigTest extends TestCase
{
    private $requestConfig;

    function setUp()
    {
        $this->requestConfig = new RequestConfig('POST', 'http://example.com', ['foo' => 'bar']);
    }

    /** @test */
    function it_implements_request_config_interface()
    {
        $this->assertInstanceOf(RequestConfigInterface::class, $this->requestConfig);
    }

    /** @test */
    function it_can_get_method()
    {
        $this->assertEquals('POST', $this->requestConfig->getMethod());
    }

    /** @test */
    function it_can_get_uri()
    {
        $this->assertEquals('http://example.com', $this->requestConfig->getUri());
    }

    /** @test */
    function it_can_get_options()
    {
        $this->assertSame(['foo' => 'bar'], $this->requestConfig->getOptions());
    }
}
