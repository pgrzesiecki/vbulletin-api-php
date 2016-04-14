<?php

namespace Signes\vBApi\Connector;

/**
 * Interface ConnectorInterface
 *
 * @package Signes\vBApi\Connector
 */
interface ConnectorInterface
{
    /**
     * Method name for GET requests.
     */
    const METHOD_GET = 'GET';

    /**
     * Method name for POST requests.
     */
    const METHOD_POST = 'POST';

    /**
     * @param array $params
     * @return mixed
     */
    public function sendGetRequest(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function sendPostRequest(array $params);
}