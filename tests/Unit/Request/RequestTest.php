<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Request;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\Request;
use Risan\OAuth1\Request\RequestInterface;

class RequestTest extends TestCase
{
    private Request $request;

    private $requestConfig;

    protected function setUp(): void
    {
        $this->request = new Request('POST', 'http://example.com', ['foo' => 'bar']);
    }

    #[Test]
    public function it_implements_request_interface()
    {
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    #[Test]
    public function it_can_get_method()
    {
        $this->assertEquals('POST', $this->request->getMethod());
    }

    #[Test]
    public function it_can_get_uri()
    {
        $this->assertEquals('http://example.com', $this->request->getUri());
    }

    #[Test]
    public function it_can_get_options()
    {
        $this->assertSame(['foo' => 'bar'], $this->request->getOptions());
    }
}
