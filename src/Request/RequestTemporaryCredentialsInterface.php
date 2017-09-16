<?php

namespace Risan\OAuth1\Request;

interface RequestTemporaryCredentialsInterface
{
    /**
     * Get url for obtaining temporary credentials.
     *
     * @return string
     */
    public function getTemporaryCredentialsUrl();

    /**
     * Get authorization header for obtaining temporary credentials.
     *
     * @return string
     */
    public function getTemporaryCredentialsAuthorizationHeader();
}
