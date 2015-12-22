<?php

namespace OAuth1Client;

use GuzzleHttp\Client as Guzzle;
use OAuth1Client\Contracts\HttpClientInterface;

class HttpClient extends Guzzle implements HttpClientInterface {
}
