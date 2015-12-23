<?php

use OAuth1Client\Signatures\HMACSHA1Signature;
use OAuth1Client\Credentials\ClientCredentials;

class HMACSHA1SignatureTest extends PHPUnit_Framework_TestCase {
    protected $clientCredentials;

    protected $signature;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');

        $this->signature = new HMACSHA1Signature($this->clientCredentials);
    }

    /** @test */
    function hmac_sha1_signature_has_valid_method()
    {
        $this->assertEquals('HMAC-SHA1', $this->signature->method());
    }

    /** @test */
    function hmac_sha1_signature_has_client_credentials()
    {
        $this->assertInstanceOf(ClientCredentials::class, $this->signature->clientCredentials());
    }

    /** @test */
    function hmac_sha1_signature_has_key()
    {
        $this->assertEquals('bar&', $this->signature->key());
    }

    /** @test */
    function hmac_sha1_signature_can_hash_data()
    {
        $data = 'test';

        $hashed = hash_hmac('sha1', $data, $this->signature->key(), true);

        $this->assertEquals($hashed, $this->signature->hash($data));
    }

    /** @test */
    function hmac_sha1_signature_can_generate_base_string()
    {
        $httpVerb = 'POST';

        $uri = 'http://foo.bar';

        $parameters = ['oauth_test' => '123'];

        $parameters_query = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);

        $expected = sprintf("%s&%s&%s", $httpVerb, rawurlencode($uri), rawurlencode($parameters_query));

        $this->assertEquals($expected, $this->signature->baseString($uri, $parameters));
    }

    /** @test */
    function hmac_sha1_signature_can_sign_request()
    {
        $uri = 'http://foo.bar';

        $parameters = ['oauth_test' => '123'];

        $baseString = $this->signature->baseString($uri, $parameters);

        $expected = base64_encode($this->signature->hash($baseString));

        $this->assertEquals($expected, $this->signature->sign($uri, $parameters));
    }
}
