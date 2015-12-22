<?php

namespace OAuth1Client;

use OAuth1Client\Contracts\UpworkInterface;

class Upwork extends OAuth1Client implements UpworkInterface {
    /**
     * Temporary credentials url.
     *
     * @return string
     */
    public function temporaryCredentialsUrl()
    {
        return 'https://www.upwork.com/api/auth/v1/oauth/token/request';
    }
}
