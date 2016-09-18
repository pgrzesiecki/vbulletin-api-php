<?php

namespace Signes\vBApi\Context\User;

use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Context\Context;

/**
 * Class Login
 *
 * @package Signes\vBApi\Context\User
 */
final class Login extends Context
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Login constructor.
     *
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->setUsername($username)->setPassword($password);
    }

    /**
     * @return string
     */
    public function getApiMethod()
    {
        return 'user.login';
    }

    /**
     * @return string
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
            'username'    => $this->username,
            'md5password' => md5($this->password),
        ];
    }

    /**
     * @param $username
     * @return static
     */
    private function setUsername($username)
    {
        \Assert\that($username)->string()->notEmpty();
        $this->username = $username;

        return $this;
    }

    /**
     * @param $password
     * @return static
     */
    private function setPassword($password)
    {
        \Assert\that($password)->string()->notEmpty();
        $this->password = $password;

        return $this;
    }
}
