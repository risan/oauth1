<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Signature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Signature\BaseStringBuilder;
use Risan\OAuth1\Signature\BaseStringBuilderInterface;

class BaseStringBuilderTest extends TestCase
{
    private $uriParser;

    private $baseStringBuilder;

    private $psrUri;

    protected function setUp(): void
    {
        $this->uriParser = new UriParser;
        $this->baseStringBuilder = new BaseStringBuilder($this->uriParser);
        $this->psrUri = $this->uriParser->toPsrUri('http://example.com/path');
    }

    #[Test]
    public function it_implements_base_string_builder_interface()
    {
        $this->assertInstanceOf(BaseStringBuilderInterface::class, $this->baseStringBuilder);
    }

    #[Test]
    public function it_can_get_uri_parser()
    {
        $this->assertSame($this->uriParser, $this->baseStringBuilder->getUriParser());
    }

    #[Test]
    public function it_can_build_valid_method_component()
    {
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('POST'));

        // Can uppercase the HTTP method.
        $this->assertEquals('POST', $this->baseStringBuilder->buildMethodComponent('post'));

        // Can build custom HTTP method.
        $this->assertEquals('CUSTOM METHOD', $this->baseStringBuilder->buildMethodComponent('Custom Method'));
    }

    #[Test]
    public function it_can_build_valid_uri_component_from_string()
    {
        $this->assertEquals('http://example.com/', $this->baseStringBuilder->buildUriComponent('http://example.com'));
        $this->assertEquals('https://example.com/', $this->baseStringBuilder->buildUriComponent('https://example.com'));

        // Can build from URI with path.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path'));

        // Can build from URI with query.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com/path?foo=bar'));
    }

    #[Test]
    public function it_can_build_valid_uri_component_with_port()
    {
        // Can build from URI with default port.
        $this->assertEquals('http://example.com/path', $this->baseStringBuilder->buildUriComponent('http://example.com:80/path'));
        $this->assertEquals('https://example.com/path', $this->baseStringBuilder->buildUriComponent('https://example.com:443/path'));

        // Can build from URI with custom port.
        $this->assertEquals('http://example.com:8080/path', $this->baseStringBuilder->buildUriComponent('http://example.com:8080/path'));
    }

    #[Test]
    public function it_can_normalize_parameters()
    {
        $normalizedParameters = $this->baseStringBuilder->normalizeParameters([
            'lang' => 'en',
            'full name' => 'John Doe',
        ]);

        // Can sort and encode the paramaters.
        $this->assertSame([
            ['full%20name', 'John%20Doe'],
            ['lang', 'en'],
        ], $normalizedParameters);
    }

    #[Test]
    public function it_rejects_nested_parameters_that_cannot_match_the_wire_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->baseStringBuilder->normalizeParameters(['languages' => ['php', 'go']]);
    }

    #[Test]
    public function it_matches_php_form_encoding_for_boolean_values_and_rejects_null()
    {
        $this->assertSame([
            ['disabled', '0'],
            ['enabled', '1'],
        ], $this->baseStringBuilder->normalizeParameters([
            'enabled' => true,
            'disabled' => false,
        ]));

        $this->expectException(\InvalidArgumentException::class);
        $this->baseStringBuilder->normalizeParameters(['omitted_by_guzzle' => null]);
    }

    #[Test]
    public function it_can_build_query_string()
    {
        $queryString = $this->baseStringBuilder->buildQueryString([
            'first_name' => 'john',
            'last_name' => 'doe',
        ]);

        $this->assertEquals('first_name=john&last_name=doe', $queryString);
    }

    #[Test]
    public function it_can_build_query_string_from_repeated_parameter_pairs()
    {
        $queryString = $this->baseStringBuilder->buildQueryString([
            ['name', 'john'],
            ['languages', 'php'],
            ['languages', 'js'],
        ]);

        $this->assertEquals('name=john&languages=php&languages=js', $queryString);
    }

    #[Test]
    public function it_can_build_parameters_components()
    {
        $baseString = $this->baseStringBuilder->buildParametersComponent(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertEquals('baz=qux&foo=bar', $baseString);
    }

    #[Test]
    public function it_can_build_base_string()
    {
        $baseString = $this->baseStringBuilder->build('POST', 'http://example.com', ['foo' => 'bar']);
        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2F&foo%3Dbar', $baseString);

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
