<?php

namespace OAuth1Client\Contracts\OAuth1Flows;

interface TokenCredentialsFlowInterface {
    /**
     * Access token credentials url.
     *
     * @return string
     */
    public function tokenCredentialsUrl();
}
