<?php

namespace Signes\vBApi;

use Exception;
use Mockery;
use Mockery\Mock;
use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Exception\VBApiException;

/**
 * Class ApiTest
 *
 * @package            Signes\vBApi
 * @coversDefaultClass Signes\vBApi\vBApi
 */
class ApiTest extends TestCase
{
    /**
     * @return Mock|ConnectorInterface
     */
    protected function getConnectorMock()
    {
        $connector = Mockery::mock(ConnectorInterface::class);

        return $connector;
    }

    /**
     * @return Mock|ApiConfig
     */
    protected function getConfigurationMock()
    {
        $configuration = Mockery::mock(ApiConfig::class);

        return $configuration;
    }

    /**
     * @covers ::__construct
     * @covers ::rememberInstance
     * @covers ::getInstance
     * @throws VBApiException
     */
    public function testInstances()
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();

        $apiFirstInstance = new Api($configuration, $connector);
        $apiFirstInstance->rememberInstance('firstInstance');

        $apiSecondInstance = new Api($configuration, $connector);
        $apiSecondInstance->rememberInstance('secondInstance');

        $this->assertSame($apiFirstInstance, Api::getInstance('firstInstance'));
        $this->assertSame($apiSecondInstance, Api::getInstance('secondInstance'));
    }

    /**
     * @covers ::__construct
     * @covers ::rememberInstance
     * @covers ::getInstance
     * @throws VBApiException
     */
    public function testInstanceOverwritten()
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();

        $apiFirstInstance = new Api($configuration, $connector);
        $apiFirstInstance->rememberInstance('instanceName');

        $apiSecondInstance = new Api($configuration, $connector);
        $apiSecondInstance->rememberInstance('instanceName', true);

        $this->assertSame($apiSecondInstance, Api::getInstance('instanceName'));
    }

    /**
     * @covers ::__construct
     * @covers ::rememberInstance
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @throws VBApiException
     */
    public function testInstanceOverwrittenException()
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();

        $apiFirstInstance = new Api($configuration, $connector);
        $apiFirstInstance->rememberInstance('instanceName');

        $apiSecondInstance = new Api($configuration, $connector);
        $apiSecondInstance->rememberInstance('instanceName');
    }

    /**
     * @covers ::getApiSignatureForParams
     * @dataProvider dataApiAuthParams
     * @param string $accessToken
     * @param string $clientId
     * @param string $secret
     * @param string $apiKey
     * @param array $params
     * @param string $expected
     */
    public function testGetApiSignatureForParams($accessToken, $clientId, $secret, $apiKey, array $params, $expected)
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->once();
        $configuration->shouldReceive('getClientId')->andReturn($clientId)->once();
        $configuration->shouldReceive('getSecret')->andReturn($secret)->once();
        $configuration->shouldReceive('getApiKey')->andReturn($apiKey)->once();

        $api = new Api($configuration, $connector);
        $result = $this->callPrivateMethod($api, 'getApiSignatureForParams', $params);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::addApiAuthInfoToParams
     * @dataProvider dataApiAuthParams
     * @param string $accessToken
     * @param string $clientId
     * @param string $secret
     * @param string $apiKey
     * @param array $params
     * @param string $expected
     */
    public function testAddApiAuthInfoToParams($accessToken, $clientId, $secret, $apiKey, array $params, $expected)
    {
        $apiRandomVersion = mt_rand(100, 999);

        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->twice();
        $configuration->shouldReceive('getClientId')->andReturn($clientId)->twice();
        $configuration->shouldReceive('getSecret')->andReturn($secret)->once();
        $configuration->shouldReceive('getApiKey')->andReturn($apiKey)->once();
        $configuration->shouldReceive('getApiVersion')->andReturn($apiRandomVersion)->once();

        $api = new Api($configuration, $connector);
        $result = $this->callPrivateMethod($api, 'addApiAuthInfoToParams', $params);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('api_sig', $result);
        $this->assertArrayHasKey('api_c', $result);
        $this->assertArrayHasKey('api_s', $result);
        $this->assertArrayHasKey('api_v', $result);

        $this->assertEquals($expected, $result['api_sig']);
        $this->assertEquals($clientId, $result['api_c']);
        $this->assertEquals($accessToken, $result['api_s']);
        $this->assertEquals($apiRandomVersion, $result['api_v']);
    }

    /**
     * @covers ::testAddApiAuthInfoToParams
     */
    public function testAddApiAuthInfoToParamsWithVersion()
    {
        $apiRandomVersion = mt_rand(100, 999);

        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getAccessToken')->twice();
        $configuration->shouldReceive('getClientId')->twice();
        $configuration->shouldReceive('getSecret')->once();
        $configuration->shouldReceive('getApiKey')->once();

        $api = new Api($configuration, $connector);
        $result = $this->callPrivateMethod($api, 'addApiAuthInfoToParams', ['api_v' => $apiRandomVersion]);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('api_v', $result);
        $this->assertEquals($apiRandomVersion, $result['api_v']);
    }

    /**
     * @covers ::isConnectionInitialized
     * @dataProvider dataIsConnectionInitializedTrue
     * @param $accessToken
     * @param $secret
     */
    public function testIsConnectionInitializedTrue($accessToken, $secret)
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->atMost(1);
        $configuration->shouldReceive('getSecret')->andReturn($secret)->atMost(1);

        $api = new Api($configuration, $connector);
        $result = $this->callPrivateMethod($api, 'isConnectionInitialized');

        $this->assertTrue($result);
    }

    /**
     * @covers ::isConnectionInitialized
     * @dataProvider dataIsConnectionInitializedFalse
     * @param $accessToken
     * @param $secret
     */
    public function testIsConnectionInitializedFalse($accessToken, $secret)
    {
        $connector = $this->getConnectorMock();
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->atMost(1);
        $configuration->shouldReceive('getSecret')->andReturn($secret)->atMost(1);

        $api = new Api($configuration, $connector);
        $result = $this->callPrivateMethod($api, 'isConnectionInitialized');

        $this->assertFalse($result);
    }

    /**
     * @covers ::initApiConnection
     * @covers ::callRequest
     * @dataProvider dataInitApiConnection
     * @param string $accessToken
     * @param string $secret
     * @param int $clientId
     * @param string $apiKey
     * @param int $apiVersion
     * @param string $apiSig
     * @param array $response
     */
    public function testInitApiConnectionWithCorrectResponse(
        $accessToken,
        $secret,
        $clientId,
        $apiKey,
        $apiVersion,
        $apiSig,
        $response
    ) {
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getInitRequestParams')->andReturn([])->once();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->times(3);
        $configuration->shouldReceive('getSecret')->andReturn($secret)->twice();
        $configuration->shouldReceive('getClientId')->andReturn($clientId)->twice();
        $configuration->shouldReceive('getApiKey')->andReturn($apiKey)->once();
        $configuration->shouldReceive('getApiVersion')->andReturn($apiVersion)->once();
        $configuration->shouldReceive('setAccessDataFromApiResponse')->with($response)->once();

        $connector = $this->getConnectorMock();
        $connector->shouldReceive('sendGetRequest')->with(
            [
                'api_sig' => $apiSig,
                'api_c'   => $clientId,
                'api_s'   => $accessToken,
                'api_v'   => $apiVersion,
                'api_m'   => Api::VBULLETIN_API_INIT,
            ]
        )->andReturn($response)->once();

        /** @var Mock|Api $api */
        $api = new Api($configuration, $connector);
        $this->callPrivateMethod($api, 'initApiConnection');
    }

    /**
     * @covers ::initApiConnection
     * @covers ::callRequest
     * @dataProvider dataInitApiConnection
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @param string $accessToken
     * @param string $secret
     * @param int $clientId
     * @param string $apiKey
     * @param int $apiVersion
     * @param string $apiSig
     * @param array $response
     */
    public function testInitApiConnectionWithException(
        $accessToken,
        $secret,
        $clientId,
        $apiKey,
        $apiVersion,
        $apiSig,
        $response
    ) {
        $configuration = $this->getConfigurationMock();
        $configuration->shouldReceive('getInitRequestParams')->andReturn([])->once();
        $configuration->shouldReceive('getAccessToken')->andReturn($accessToken)->times(3);
        $configuration->shouldReceive('getSecret')->andReturn($secret)->twice();
        $configuration->shouldReceive('getClientId')->andReturn($clientId)->twice();
        $configuration->shouldReceive('getApiKey')->andReturn($apiKey)->once();
        $configuration->shouldReceive('getApiVersion')->andReturn($apiVersion)->once();
        $configuration->shouldReceive('setAccessDataFromApiResponse')->andThrow(Exception::class);

        $connector = $this->getConnectorMock();
        $connector->shouldReceive('sendGetRequest')->with(
            [
                'api_sig' => $apiSig,
                'api_c'   => $clientId,
                'api_s'   => $accessToken,
                'api_v'   => $apiVersion,
                'api_m'   => Api::VBULLETIN_API_INIT,
            ]
        )->andReturn($response)->once();

        /** @var Mock|Api $api */
        $api = new Api($configuration, $connector);
        $this->callPrivateMethod($api, 'initApiConnection');
    }

    /**
     * @return array
     */
    public function dataInitApiConnection()
    {
        return [
            [
                'accessToken',
                'secret',
                1,
                'apiKey',
                1,
                '49661b76bb869ae9f2d6872e7a2ece2e',
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
    public function dataIsConnectionInitializedTrue()
    {
        return [
            ['data', 'data'],
        ];
    }

    /**
     * @return array
     */
    public function dataIsConnectionInitializedFalse()
    {
        return [
            ['data', ''],
            ['', 'data'],
            ['', ''],
            ['data', null],
        ];
    }

    /**
     * @return array
     */
    public function dataApiAuthParams()
    {
        return [
            [
                'accessToken',
                'clientId',
                'secret',
                'apiKey',
                ['param1' => 'value1', 'param2' => 'value2'],
                '83f8361a7dca2e018e42c0c9e30627b2',
            ],
            [
                'accessToken',
                'clientId',
                'secret',
                'apiKey',
                [],
                'c31f336cc9a75cb89ea7d0b70393c4d9',
            ],
            [
                'accessToken1',
                'clientId',
                'secret',
                'apiKey',
                ['param1' => 'value1', 'param2' => 'value2'],
                '307256a57f4838cd855f9ea07767be68',
            ],
            [
                'accessToken',
                'clientId1',
                'secret',
                'apiKey',
                ['param1' => 'value1', 'param2' => 'value2'],
                'bc5ca4050ecc58adedd3d4165d79cdbe',
            ],
            [
                'accessToken',
                'clientId',
                'secret1',
                'apiKey',
                ['param1' => 'value1', 'param2' => 'value2'],
                '51c715678544b31b95b3a150862f8988',
            ],
            [
                'accessToken',
                'clientId',
                'secret',
                'apiKey1',
                ['param1' => 'value1', 'param2' => 'value2'],
                '205ad96a33a39ee8665119d07430accb',
            ],
            [
                'accessToken',
                'clientId',
                'secret',
                'apiKey',
                ['param' => 'value1', 'param2' => 'value2'],
                '5b5a5f94f9bf18c54ad3cd858d19ee6f',
            ],
        ];
    }
}
