<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Signers\HmacSha1Signer;

class HmacSha1SignerTest extends TestCase {
    protected $signer;

    function setUp()
    {
        $this->signer = new HmacSha1Signer('consumer_secret');
    }

    /** @test */
    function hmac_sha1_signer_has_consumer_secret()
    {
        $this->assertEquals('consumer_secret', $this->signer->consumerSecret());
    }

    /** @test */
    function hmac_sha1_signer_can_set_token_secret()
    {
        $this->assertEmpty($this->signer->tokenSecret());

        $this->assertInstanceOf(HmacSha1Signer::class, $this->signer->setTokenSecret('token_secret'));

        $this->assertEquals('token_secret', $this->signer->tokenSecret());
    }

    /** @test */
    function hmac_sha1_signer_has_valid_method()
    {
        $this->assertEquals('HMAC-SHA1', $this->signer->method());
    }

    /** @test */
    function hmac_sha1_signer_has_key()
    {
        $this->signer->setTokenSecret(null);

        $this->assertEquals('consumer_secret&', $this->signer->key());

        $this->signer->setTokenSecret('token_secret');

        $this->assertEquals('consumer_secret&token_secret', $this->signer->key());
    }

    /** @test */
    function hmac_sha1_signer_can_hash_data()
    {
        $data = 'test';

        $expected = hash_hmac('sha1', $data, $this->signer->key(), true);

        $this->assertEquals($expected, $this->signer->hash($data));
    }

    /** @test */
    function hmac_sha1_signer_can_generate_base_string()
    {
        $httpVerb = 'POST';

        $uri = 'http://foo.bar';

        $parameters = ['oauth_test' => '123'];

        $parameters_query = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);

        $expected = sprintf("%s&%s&%s", $httpVerb, rawurlencode($uri), rawurlencode($parameters_query));

        $this->assertEquals($expected, $this->signer->baseString($uri, $parameters, $httpVerb));
    }

    /** @test */
    function hmac_sha1_signer_can_sign_request()
    {
        $uri = 'http://foo.bar';

        $parameters = ['oauth_test' => '123'];

        $baseString = $this->signer->baseString($uri, $parameters);

        $expected = base64_encode($this->signer->hash($baseString));

        $this->assertEquals($expected, $this->signer->sign($uri, $parameters));
    }
}
