<?php

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Signature\BaseStringBuilder;

class BaseStringBuilderTest extends TestCase
{
    private $baseStringBuilder;
    private $psrUri;

    function setUp()
    {
        $this->baseStringBuilder = new BaseStringBuilder;
        $this->psrUri = new Uri('http://example.com/path');
    }

    /** @test */
    function base_string_builder_can_build_valid_method_component()
    {
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('POST'));

        // Can uppercase the HTTP method.
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('post'));

        // Can build custom HTTP method.
        $this->assertEquals('CUSTOM METHOD', $this->baseStringBuilder->buildMethodComponent('Custom Method'));
    }

    /** @test */
    function base_string_builder_can_build_valid_uri_component_from_string()
    {
        $this->assertEquals('http://example.com', $this->baseStringBuilder->buildUriComponent('http://example.com'));
        $this->assertEquals('https://example.com', $this->baseStringBuilder->buildUriComponent('https://example.com'));

        // Can build from URI with path.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path'));

        // Can build from URI with query.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path?foo=bar'));
    }

    /** @test */
    function base_string_builder_can_build_valid_uri_component_with_port()
    {
        // Can build from URI with default port.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com:80/path'));
        $this->assertEquals('https://example.com/path', $this->baseStringBuilder->buildUriComponent('https://example.com:443/path'));

        // Can build from URI with custom port.
        $this->assertEquals('http://example.com:8080/path', $this->baseStringBuilder->buildUriComponent('http://example.com:8080/path'));
    }

    /** @test */
    function base_string_builder_can_build_valid_uri_component_from_psr_uri()
    {
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent($this->psrUri));
    }
}
