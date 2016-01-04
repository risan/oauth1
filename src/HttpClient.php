<?php

namespace OAuth1;

use GuzzleHttp\Client as Guzzle;
use OAuth1\Contracts\HttpClientInterface;

class HttpClient extends Guzzle implements HttpClientInterface
{
}
