<?php

namespace Risan\OAuth1\Request;

interface RequestConfigInterface
{
    /**
     * Get the request HTTP method.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getUri();

    /**
     * Get the request options.
     *
     * @return array
     */
    public function getOptions();
}
