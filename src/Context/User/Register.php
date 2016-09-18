<?php

namespace Signes\vBApi\Context\User;

use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Context\Context;

/**
 * Class Register
 *
 * @package Signes\vBApi\Context\User
 */
final class Register extends Context
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $userFields;

    /**
     * @var \DateTimeImmutable
     */
    private $birthday;

    /**
     * @var array
     */
    private $facebookData;

    /**
     * FetchByEmail constructor.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param array $userFields
     * @param \DateTimeImmutable $birthday
     * @param array $facebookData
     */
    public function __construct(
        $username,
        $email,
        $password,
        array $userFields = [],
        \DateTimeImmutable $birthday = null,
        array $facebookData = []
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = md5($password);
        $this->userFields = $userFields;
        $this->birthday = $birthday;
        $this->facebookData = $facebookData;
    }

    /**
     * @return string
     */
    public function getApiMethod()
    {
        return 'register_addmember';
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return ConnectorInterface::METHOD_POST;
    }

    /**
     * @return mixed
     */
    public function getRequestParams()
    {
        $requestParams = [
            'api_v'               => 1,
            'agree'               => true,
            'username'            => $this->username,
            'email'               => $this->email,
            'emailconfirm'        => $this->email,
            'password_md5'        => $this->password,
            'passwordconfirm_md5' => $this->password,
        ];

        if (!empty($this->userFields)) {
            $requestParams['userfield'] = $this->userFields;
        }

        $requestParams = $this->addBirthdayParam($requestParams);
        $requestParams = $this->addFacebookRequestParams($requestParams);

        return $requestParams;
    }

    /**
     * @param array $requestParams
     * @return array
     */
    private function addBirthdayParam(array $requestParams)
    {
        if ($this->birthday) {
            $requestParams['day'] = $this->birthday->format('d');
            $requestParams['month'] = $this->birthday->format('m');
            $requestParams['year'] = $this->birthday->format('Y');
        }

        return $requestParams;
    }

    /**
     * @param array $requestParams
     * @return array
     */
    private function addFacebookRequestParams(array $requestParams)
    {
        if (!empty($this->facebookData) && isset($this->facebookData['name']) && isset($this->facebookData['id'])) {
            $requestParams['fbname'] = $this->facebookData['name'];
            $requestParams['fbuserid'] = $this->facebookData['id'];
        }

        return $requestParams;
    }
}
