<?php

namespace Signes\vBApi;

/**
 * Class ApiConfigTest
 *
 * @package            Signes\vBApi
 * @coversDefaultClass Signes\vBApi\ApiConfig
 */
class ApiConfigTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setApiKey
     * @covers ::setUniqueName
     * @covers ::setClientName
     * @covers ::setClientVersion
     * @covers ::setPlatformName
     * @covers ::setPlatformVersion
     * @covers ::getApiKey
     * @covers ::getUniqueName
     * @covers ::getClientName
     * @covers ::getClientVersion
     * @covers ::getPlatformName
     * @covers ::getPlatformVersion
     * @covers ::getInitRequestParams
     * @dataProvider dataCreateNewInstance
     * @param string $apiKey
     * @param string $uniqueName
     * @param string $clientName
     * @param string $clientVersion
     * @param string $platformName
     * @param string $platformVersion
     */
    public function testCreateNewInstance(
        $apiKey,
        $uniqueName,
        $clientName,
        $clientVersion,
        $platformName,
        $platformVersion
    ) {
        $apiConfig = new ApiConfig($apiKey, $uniqueName, $clientName, $clientVersion, $platformName, $platformVersion);

        $this->assertEquals($apiKey, $apiConfig->getApiKey());
        $this->assertEquals($uniqueName, $apiConfig->getUniqueName());
        $this->assertEquals($clientName, $apiConfig->getClientName());
        $this->assertEquals($clientVersion, $apiConfig->getClientVersion());
        $this->assertEquals($platformName, $apiConfig->getPlatformName());
        $this->assertEquals($platformVersion, $apiConfig->getPlatformVersion());

        $this->assertEquals(
            [
                'uniqueid'        => $uniqueName,
                'clientname'      => $clientName,
                'clientversion'   => $clientVersion,
                'platformname'    => $platformName,
                'platformversion' => $platformVersion,
            ],
            $apiConfig->getInitRequestParams()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::setApiKey
     * @covers ::setUniqueName
     * @covers ::setClientName
     * @covers ::setClientVersion
     * @covers ::setPlatformName
     * @covers ::setPlatformVersion
     * @dataProvider dataCreateNewInstanceException
     * @expectedException \Assert\InvalidArgumentException
     * @param string $apiKey
     * @param string $uniqueName
     * @param string $clientName
     * @param string $clientVersion
     * @param string $platformName
     * @param string $platformVersion
     */
    public function testCreateNewInstanceException(
        $apiKey,
        $uniqueName,
        $clientName,
        $clientVersion,
        $platformName,
        $platformVersion
    ) {
        new ApiConfig($apiKey, $uniqueName, $clientName, $clientVersion, $platformName, $platformVersion);
    }

    /**
     * @covers ::setAccessDataFromApiResponse
     * @covers ::setAccessToken
     * @covers ::setSecret
     * @covers ::setApiVersion
     * @covers ::setClientId
     * @covers ::getAccessToken
     * @covers ::getSecret
     * @covers ::getApiVersion
     * @covers ::getClientId
     * @dataProvider dataSetAccessDataFromApiResponse
     * @param array $response
     */
    public function testSetAccessDataFromApiResponse(array $response)
    {
        $apiConfig = new ApiConfig('a', 'b', 'c', 'd', 'e', 'f');
        $apiConfig->setAccessDataFromApiResponse($response);

        $this->assertEquals($response['apiaccesstoken'], $apiConfig->getAccessToken());
        $this->assertEquals($response['secret'], $apiConfig->getSecret());
        $this->assertEquals($response['apiversion'], $apiConfig->getApiVersion());
        $this->assertEquals($response['apiclientid'], $apiConfig->getClientId());
    }

    /**
     * @covers ::setAccessDataFromApiResponse
     * @covers ::setAccessToken
     * @covers ::setSecret
     * @covers ::setApiVersion
     * @covers ::setClientId
     * @dataProvider dataSetAccessDataFromApiResponseException
     * @expectedException \Assert\InvalidArgumentException
     * @param array $response
     */
    public function testSetAccessDataFromApiResponseException(array $response)
    {
        $apiConfig = new ApiConfig('a', 'b', 'c', 'd', 'e', 'f');
        $apiConfig->setAccessDataFromApiResponse($response);
    }

    /**
     * @return array
     */
    public function dataCreateNewInstance()
    {
        return [
            ['a', 'b', 'c', 'd', 'e', 'f'],
        ];
    }

    /**
     * @return array
     */
    public function dataCreateNewInstanceException()
    {
        return [
            [1, 'b', 'c', 'd', 'e', 'f'],
            ['a', 2, 'c', 'd', 'e', 'f'],
            ['a', 'b', 3, 'd', 'e', 'f'],
            ['a', 'b', 'c', 4, 'e', 'f'],
            ['a', 'b', 'c', 'd', 5, 'f'],
            ['a', 'b', 'c', 'd', 'e', 6],
        ];
    }

    /**
     * @return array
     */
    public function dataSetAccessDataFromApiResponse()
    {
        return [
            [
                [
                    'apiaccesstoken' => 'a',
                    'secret'         => 'b',
                    'apiversion'     => 1,
                    'apiclientid'    => 2,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataSetAccessDataFromApiResponseException()
    {
        return [
            [
                [
                    'apiaccesstoken' => 'a',
                    'secret'         => 'b',
                    'apiversion'     => 'c',
                    'apiclientid'    => 2,
                ],
                [
                    'apiaccesstoken' => 'a',
                    'secret'         => 'b',
                    'apiversion'     => 1,
                    'apiclientid'    => 'd',
                ],
                [
                    'apiaccesstoken' => 1,
                    'secret'         => 'b',
                    'apiversion'     => 1,
                    'apiclientid'    => 2,
                ],
                [
                    'apiaccesstoken' => 'a',
                    'secret'         => 2,
                    'apiversion'     => 1,
                    'apiclientid'    => 2,
                ],
            ],
        ];
    }
}
