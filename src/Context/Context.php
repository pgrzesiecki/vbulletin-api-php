<?php

namespace Signes\vBApi\Context;

/**
 * Class Context
 *
 * @package Signes\vBApi\Context
 */
abstract class Context
{
    /**
     * @return string
     */
    abstract public function getApiMethod();

    /**
     * @return string
     */
    abstract public function getRequestMethod();

    /**
     * @return array
     */
    abstract public function getRequestParams();

    /**
     * Parse response from vB API to required format.
     * By default, it return RAW API response without any action.
     *
     * @param array $response
     * @return mixed
     */
    public function parseResponse(array $response)
    {
        return $response;
    }
}
