<?php

namespace Signes\vBApi\Context\User;

use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Context\Context;

/**
 * Class FetchCurrentUserInfo
 *
 * @package Signes\vBApi\Context\User
 */
class FetchCurrentUserInfo extends Context
{
    /**
     * @return mixed
     */
    public function getApiMethod()
    {
        return 'user.fetchCurrentUserinfo';
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return ConnectorInterface::METHOD_GET;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return [];
    }
}
