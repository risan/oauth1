<?php

namespace Risan\OAuth1\Test\Unit\Signature;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Signature\BaseStringBuilder;
use Risan\OAuth1\Signature\BaseStringBuilderInterface;

class BaseStringBuilderTest extends TestCase
{
    private $uriParser;
    private $baseStringBuilder;
    private $psrUri;

    function setUp()
    {
        $this->uriParser = new UriParser;
        $this->baseStringBuilder = new BaseStringBuilder($this->uriParser);
        $this->psrUri = $this->uriParser->toPsrUri('http://example.com/path');
    }

    /** @test */
    function it_implements_base_string_builder_interface()
    {
        $this->assertInstanceOf(BaseStringBuilderInterface::class, $this->baseStringBuilder);
    }

    /** @test */
    function it_can_get_uri_parser()
    {
        $this->assertSame($this->uriParser, $this->baseStringBuilder->getUriParser());
    }

    /** @test */
    function it_can_build_valid_method_component()
    {
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('POST'));

        // Can uppercase the HTTP method.
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('post'));

        // Can build custom HTTP method.
        $this->assertEquals('CUSTOM METHOD', $this->baseStringBuilder->buildMethodComponent('Custom Method'));
    }

    /** @test */
    function it_can_build_valid_uri_component_from_string()
    {
        $this->assertEquals('http://example.com', $this->baseStringBuilder->buildUriComponent('http://example.com'));
        $this->assertEquals('https://example.com', $this->baseStringBuilder->buildUriComponent('https://example.com'));

        // Can build from URI with path.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path'));

        // Can build from URI with query.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path?foo=bar'));
    }

    /** @test */
    function it_can_build_valid_uri_component_with_port()
    {
        // Can build from URI with default port.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com:80/path'));
        $this->assertEquals('https://example.com/path', $this->baseStringBuilder->buildUriComponent('https://example.com:443/path'));

        // Can build from URI with custom port.
        $this->assertEquals('http://example.com:8080/path', $this->baseStringBuilder->buildUriComponent('http://example.com:8080/path'));
    }

    /** @test */
    function it_can_normalize_parameters()
    {
        $normalizedParameters = $this->baseStringBuilder->normalizeParameters([
            'lang' => 'en',
            'full name' => 'John Doe',
        ]);

        // Can sort and encode the paramaters.
        $this->assertSame([
            'full%20name' => 'John%20Doe',
            'lang' => 'en',
        ], $normalizedParameters);
    }

    /** @test */
    function it_can_normalize_multi_dimensional_parameters()
    {
        $normalizedParameters = $this->baseStringBuilder->normalizeParameters([
            'lang' => 'en',
            'full name' => 'John Doe',
            'programming languages' => ['php', 'go lang'],
            'location' => [
                'home town' => 'Cimahi',
                'home' => 'Stockholm Sweden',
            ],
        ]);

        $this->assertSame([
            'full%20name' => 'John%20Doe',
            'lang' => 'en',
            'location' => [
                'home' => 'Stockholm%20Sweden',
                'home%20town' => 'Cimahi',
            ],
            'programming%20languages' => ['php', 'go%20lang'],
        ], $normalizedParameters);
    }

    /** @test */
    function it_can_build_query_string()
    {
        $queryString = $this->baseStringBuilder->buildQueryString([
            'first_name' => 'john',
            'last_name' => 'doe',
        ]);

        $this->assertEquals('first_name=john&last_name=doe', $queryString);
    }

    /** @test */
    function it_can_build_query_string_from_multi_dimensional_array()
    {
        $queryString = $this->baseStringBuilder->buildQueryString([
            'name' => 'john',
            'languages' => ['php', 'js'],
            'location' => [
                'city' => 'stockholm',
                'country' => 'sweden',
            ],
        ]);

        $this->assertEquals('name=john&languages[0]=php&languages[1]=js&location[city]=stockholm&location[country]=sweden', $queryString);
    }

    /** @test */
    function it_can_build_parameters_components()
    {
        $baseString = $this->baseStringBuilder->buildParametersComponent(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertEquals('baz=qux&foo=bar', $baseString);
    }

    /** @test */
    function it_can_build_base_string()
    {
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com&foo%3Dbar', $baseString);

        // URI with path.
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com/path', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);

        // With query parameter.
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com/path?foo=bar', ['baz' => 'qux']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&baz%3Dqux%26foo%3Dbar', $baseString);

        // Can uppercase the HTTP method.
        $baseString = $this->baseStringBuilder->build('post', 'http://example.com/path', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);

        // Can uppercase & encode custom HTTP method.
        $baseString = $this->baseStringBuilder->build('Custom Method', 'http://example.com/path', ['foo' => 'bar']);
        $this->assertEquals('CUSTOM%20METHOD&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);

        // Can remove default HTTP port.
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com:80/path', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);

        $baseString = $this->baseStringBuilder->build('POST', 'https://example.com:443/path', ['foo' => 'bar']);
        $this->assertEquals('POST&https%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);

        // Can keep custom HTTP port.
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com:8080/path', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%3A8080%2Fpath&foo%3Dbar', $baseString);
    }
}
