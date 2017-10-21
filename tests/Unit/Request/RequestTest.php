<?php

namespace Risan\OAuth1\Test\Unit\Request;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\Request;
use Risan\OAuth1\Request\RequestInterface;

class RequestTest extends TestCase
{
    private $requestConfig;

    function setUp()
    {
        $this->request = new Request('POST', 'http://example.com', ['foo' => 'bar']);
    }

    /** @test */
    function it_implements_request_interface()
    {
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    /** @test */
    function it_can_get_method()
    {
        $this->assertEquals('POST', $this->request->getMethod());
    }

    /** @test */
    function it_can_get_uri()
    {
        $this->assertEquals('http://example.com', $this->request->getUri());
    }

    /** @test */
    function it_can_get_options()
    {
        $this->assertSame(['foo' => 'bar'], $this->request->getOptions());
    }
}
