<?php

namespace Signes\vBApi;

use Assert\AssertionFailedException;

/**
 * Class ApiConfig
 *
 * @package Signes\vBApi
 */
class ApiConfig
{
    /**
     * @var string
     */
    private $uniqueName;

    /**
     * @var string
     */
    private $clientName;

    /**
     * @var string
     */
    private $clientVersion;

    /**
     * @var string
     */
    private $platformName;

    /**
     * @var string
     */
    private $platformVersion;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var int
     */
    private $apiVersion;

    /**
     * ApiConfig constructor.
     *
     * @param string $apiKey
     * @param string $uniqueName
     * @param string $clientName
     * @param string $clientVersion
     * @param string $platformName
     * @param string $platformVersion
     */
    public function __construct($apiKey, $uniqueName, $clientName, $clientVersion, $platformName, $platformVersion)
    {
        $this->setApiKey($apiKey)->setUniqueName($uniqueName);
        $this->setClientName($clientName)->setClientVersion($clientVersion);
        $this->setPlatformName($platformName)->setPlatformVersion($platformVersion);
    }

    /**
     * @param string $accessToken
     * @return static
     * @throws AssertionFailedException
     */
    protected function setAccessToken($accessToken)
    {
        \Assert\that($accessToken, "Api access token must be non empty string. Given: '{$accessToken}'")
            ->notEmpty()
            ->string();
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $secret
     * @return static
     * @throws AssertionFailedException
     */
    protected function setSecret($secret)
    {
        \Assert\that($secret, "Api secret must be non empty string. Given: '{$secret}'")->notEmpty()->string();
        $this->secret = $secret;

        return $this;
    }

    /**
     * @param int $clientId
     * @return static
     * @throws AssertionFailedException
     */
    protected function setClientId($clientId)
    {
        \Assert\that($clientId, "Client ID must be integer greater than zero. Given: '{$clientId}'")
            ->notEmpty()
            ->integerish()
            ->greaterThan(0);
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @param string $apiKey
     * @return static
     * @throws AssertionFailedException
     */
    protected function setApiKey($apiKey)
    {
        \Assert\that($apiKey, "Api key must be non empty string. Given: '{$apiKey}'")->notEmpty()->string();
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param int $apiVersion
     * @return static
     * @throws AssertionFailedException
     */
    protected function setApiVersion($apiVersion)
    {
        \Assert\that($apiVersion)->notEmpty()->integerish()->greaterThan(0);
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * @param string $uniqueName
     * @return static
     */
    protected function setUniqueName($uniqueName)
    {
        \Assert\that($uniqueName, "Unique name must be non empty string. Given: '{$uniqueName}'")->notEmpty()->string();
        $this->uniqueName = $uniqueName;

        return $this;

    }

    /**
     * @param string $clientName
     * @return static
     */
    protected function setClientName($clientName)
    {
        \Assert\that($clientName, "Client name must be non empty string. Given: '{$clientName}'")->notEmpty()->string();
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * @param string $clientVersion
     * @return static
     */
    protected function setClientVersion($clientVersion)
    {
        \Assert\that($clientVersion, "Client version must be non empty string. Given: '{$clientVersion}'")
            ->notEmpty()
            ->string();
        $this->clientVersion = $clientVersion;

        return $this;

    }

    /**
     * @param string $platformName
     * @return static
     */
    protected function setPlatformName($platformName)
    {
        \Assert\that($platformName, "Platform name must be non empty string. Given: '{$platformName}'")
            ->notEmpty()
            ->string();
        $this->platformName = $platformName;

        return $this;
    }

    /**
     * @param string $platformVersion
     * @return static
     */
    protected function setPlatformVersion($platformVersion)
    {
        \Assert\that($platformVersion, "Platform version must be non empty string. Given: '{$platformVersion}'")
            ->notEmpty()
            ->string();
        $this->platformVersion = $platformVersion;

        return $this;
    }

    /**
     * Return array of parameters required by vB API to initialize connection.
     *
     * @return array
     */
    public function getInitRequestParams()
    {
        return [
            'uniqueid'        => $this->getUniqueName(),
            'clientname'      => $this->getClientName(),
            'clientversion'   => $this->getClientVersion(),
            'platformname'    => $this->getPlatformName(),
            'platformversion' => $this->getPlatformVersion(),
        ];
    }

    /**
     * Set authorization data based on vB API init response.
     *
     * @param array $response
     */
    public function setAccessDataFromApiResponse(array $response)
    {
        $this->setAccessToken(isset($response['apiaccesstoken']) ? $response['apiaccesstoken'] : null);
        $this->setSecret(isset($response['secret']) ? $response['secret'] : null);
        $this->setApiVersion(isset($response['apiversion']) ? $response['apiversion'] : null);
        $this->setClientId(isset($response['apiclientid']) ? $response['apiclientid'] : null);
    }

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->uniqueName;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @return string
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * @return string
     */
    public function getPlatformName()
    {
        return $this->platformName;
    }

    /**
     * @return string
     */
    public function getPlatformVersion()
    {
        return $this->platformVersion;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return int
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }
}
