<?php

use OAuth1Client\Client;
use OAuth1Client\Contracts\HttpClientInterface;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

class ClientTest extends PHPUnit_Framework_TestCase {
    protected $client;

    function setUp()
    {
        $this->client = new Client();
    }

    /** @test */
    function client_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->client->httpClient());
    }

    /** @test */
    function client_can_create_temporary_credentials()
    {
        $this->assertInstanceOf(TemporaryCredentialsInterface::class, $this->client->temporaryCredentials());
    }
}
