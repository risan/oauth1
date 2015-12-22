<?php

use OAuth1Client\Credentials\TemporaryCredentials;

class TemporaryCredentialsTest extends PHPUnit_Framework_TestCase {
    protected $temporaryCredentials;

    function setUp()
    {
        $this->temporaryCredentials = new TemporaryCredentials('foo', 'bar');
    }

    /** @test */
    function temporary_credentials_has_identifier()
    {
        $this->assertEquals('foo', $this->temporaryCredentials->identifier());
    }

    /** @test */
    function temporary_credentials_has_secret()
    {
        $this->assertEquals('bar', $this->temporaryCredentials->secret());
    }

    /** @test */
    function temporary_credentials_not_exposing_secret_property_when_casting_to_string()
    {
        $this->assertEquals('foo', (string) $this->temporaryCredentials);
    }
}
