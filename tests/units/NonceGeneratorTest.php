<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\NonceGenerator;

class NonceGeneratorTest extends TestCase
{
    private $nonceGenerator;

    function setUp()
    {
        $this->nonceGenerator = new NonceGenerator;
    }

    /** @test */
    function nonce_generator_can_generate_base_64_encoded_random_bytes()
    {
        // Must be a string.
        $this->assertInternalType('string', $this->nonceGenerator->base64EncodedRandomBytes(10));

        // Base64 encoded ends with '='.
        $this->assertStringEndsWith('=', $this->nonceGenerator->base64EncodedRandomBytes(10));

        // Must be random.
        $this->assertNotEquals(
            $this->nonceGenerator->base64EncodedRandomBytes(10),
            $this->nonceGenerator->base64EncodedRandomBytes(10)
        );
    }

    /** @test */
    function nonce_generator_can_extract_alpha_numeric_from_base_64_encoded_string()
    {
        $this->assertEquals('foobarbaz', $this->nonceGenerator->extractAlphaNumericFromBase64EncodedString('foo+bar/baz='));
    }

    /** @test */
    function nonce_generator_can_generate_random_string()
    {
        // Must be alphanumeric with 32 characters.
        $this->assertRegExp('/^[\w]{32}$/', $this->nonceGenerator->generate(32));

        // Must be random.
        $this->assertNotEquals(
            $this->nonceGenerator->generate(),
            $this->nonceGenerator->generate()
        );
    }
}
