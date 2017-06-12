<?php

namespace Risan\OAuth1;

use GuzzleHttp\Client as Guzzle;
use Risan\OAuth1\Contracts\HttpClientInterface;

class HttpClient extends Guzzle implements HttpClientInterface
{
}
