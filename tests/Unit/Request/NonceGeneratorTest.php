<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Request;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\NonceGenerator;
use Risan\OAuth1\Request\NonceGeneratorInterface;

class NonceGeneratorTest extends TestCase
{
    private $nonceGenerator;

    protected function setUp(): void
    {
        $this->nonceGenerator = new NonceGenerator;
    }

    #[Test]
    public function it_implements_nonce_generator_interface()
    {
        $this->assertInstanceOf(NonceGeneratorInterface::class, $this->nonceGenerator);
    }

    #[Test]
    public function it_can_generate_base_64_encoded_random_bytes()
    {
        // Must be a string.
        $this->assertIsString($this->nonceGenerator->base64EncodedRandomBytes(10));

        // Base64 encoded ends with '='.
        $this->assertStringEndsWith('=', $this->nonceGenerator->base64EncodedRandomBytes(10));

        // Must be random.
        $this->assertNotEquals(
            $this->nonceGenerator->base64EncodedRandomBytes(10),
            $this->nonceGenerator->base64EncodedRandomBytes(10)
        );
    }

    #[Test]
    public function it_can_extract_alpha_numeric_from_base_64_encoded_string()
    {
        $this->assertEquals('foobarbaz', $this->nonceGenerator->extractAlphaNumericFromBase64EncodedString('foo+bar/baz='));
    }

    #[Test]
    public function it_can_generate_random_string()
    {
        // Must be alphanumeric with 32 characters.
        $this->assertMatchesRegularExpression('/^[\w]{32}$/', $this->nonceGenerator->generate(32));

        // Must be random.
        $this->assertNotEquals(
            $this->nonceGenerator->generate(),
            $this->nonceGenerator->generate()
        );
    }

    #[Test]
    public function it_rejects_an_empty_nonce_length()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->nonceGenerator->generate(0);
    }
}
