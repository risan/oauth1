<?php

use OAuth1Client\Upwork;
use OAuth1Client\Contracts\HttpClientInterface;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

class UpworkTest extends PHPUnit_Framework_TestCase {
    protected $client;

    protected $temporaryCredentialsUrl;

    function setUp()
    {
        $this->client = new Upwork();

        $this->temporaryCredentialsUrl = 'https://www.upwork.com/api/auth/v1/oauth/token/request';
    }

    /** @test */
    function upwork_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->client->httpClient());
    }

    function upwork_has_temporary_credentials_url()
    {
        return $this->assertEquals($this->temporaryCredentialsUrl, $this->client->temporaryCredentialsUrl());
    }

    /** @test */
    function upwork_can_create_temporary_credentials()
    {
        $this->assertInstanceOf(TemporaryCredentialsInterface::class, $this->client->temporaryCredentials());
    }
}
