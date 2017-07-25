<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Signature\CanBuildBaseString;
use Risan\OAuth1\Signature\BaseStringBuilderInterface;

class CanBuildBaseStringTest extends TestCase
{
    private $canBuildBaseStringStub;

    function setUp()
    {
        $this->canBuildBaseStringStub = $this->getMockForTrait(CanBuildBaseString::class);
    }

    /** @test */
    function can_build_base_string_trait_can_get_base_string_builder_interface_instance()
    {
        $this->assertInstanceOf(BaseStringBuilderInterface::class, $this->canBuildBaseStringStub->getBaseStringBuilder());
    }

    /** @test */
    function can_build_base_string_trait_can_build_base_string()
    {
        $baseString = $this->canBuildBaseStringStub->buildBaseString('http://example.com/path', ['foo' => 'bar'], 'POST');

        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $canBuildBaseStringStub);
    }
}
