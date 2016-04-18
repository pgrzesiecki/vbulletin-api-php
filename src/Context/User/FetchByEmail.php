<?php

namespace Signes\vBApi\Context\User;

use Assert\Assertion;
use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Context\Context;

/**
 * Class FetchByEmail
 *
 * @package Signes\vBApi\Context\User
 */
final class FetchByEmail extends Context
{
    /**
     * @var string
     */
    private $email;

    /**
     * FetchByEmail constructor.
     *
     * @param string $email
     */
    public function __construct($email)
    {
        $this->setEmail($email);
    }

    /**
     * @return mixed
     */
    public function getApiMethod()
    {
        return 'user.fetchByEmail';
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return ConnectorInterface::METHOD_POST;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return [
            'email' => $this->email,
        ];
    }

    /**
     * @param string $email
     * @return static
     */
    private function setEmail($email)
    {
        Assertion::email($email);
        $this->email = $email;

        return $this;
    }
}
