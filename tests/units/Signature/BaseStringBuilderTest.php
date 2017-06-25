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

    /** @test */
    function base_string_builder_can_normalize_parameters()
    {
        $normalizedParameters = $this->baseStringBuilder->normalizeParameters([
            'name' => 'John',
            'full name' => 'John Doe',
        ]);

        // Can sort and encode the paramaters.
        $this->assertSame([
            'full%20name' => 'John%20Doe',
            'name' => 'John',
        ], $normalizedParameters);
    }

    /** @test */
    function base_string_builder_can_normalize_multi_dimensional_parameters()
    {
        $normalizedParameters = $this->baseStringBuilder->normalizeParameters([
            'name' => 'John',
            'full name' => 'John Doe',
            'programming languages' => ['php', 'go lang'],
            'location' => [
                'home town' => 'Cimahi',
                'home' => 'Stockholm Sweden',
            ],
        ]);

        $this->assertSame([
            'full%20name' => 'John%20Doe',
            'location' => [
                'home' => 'Stockholm%20Sweden',
                'home%20town' => 'Cimahi',
            ],
            'name' => 'John',
            'programming%20languages' => ['php', 'go%20lang'],
        ], $normalizedParameters);
    }

    /** @test */
    function base_string_builder_can_build_query_string()
    {
        $queryString = $this->baseStringBuilder->buildQueryString([
            'first_name' => 'john',
            'last_name' => 'doe',
        ]);

        $this->assertEquals('first_name=john&last_name=doe', $queryString);
    }

    /** @test */
    function base_string_builder_can_build_query_string_from_multi_dimensional_array()
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
}
