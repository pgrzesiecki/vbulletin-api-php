<?php

namespace Signes\vBApi\Connector\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Mockery;
use Mockery\Mock;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Signes\vBApi\TestCase;

/**
 * Class GuzzleProvider.
 *
 * @package            Signes\vBApi\Connector\Provider
 * @coversDefaultClass Signes\vBApi\Connector\Provider\GuzzleProvider
 */
class GuzzleProviderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setGuzzleClient
     * @covers ::initGuzzleClientInstance
     */
    public function testInitializeConnectorClient()
    {
        $client = new GuzzleProvider('http://example.com/');
        $this->assertObjectHasAttribute('guzzleClient', $client);
        $this->assertInstanceOf(Client::class, $this->getPrivateParam($client, 'guzzleClient'));

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $client->setGuzzleClient($clientMock);
        $this->assertAttributeSame($clientMock, 'guzzleClient', $client);
    }

    /**
     * @covers ::sendGetRequest
     */
    public function testSendGetRequest()
    {
        $requestParams = ['param1' => 'value1'];
        $expectedResult = ['paramX' => 'valueY'];
        $debug = false;

        /** @var Mock|ResponseInterface $clientResponseMock */
        $clientResponseMock = Mockery::mock(ResponseInterface::class);
        $clientResponseMock->shouldReceive('getBody')->once()->andReturn(json_encode($expectedResult));

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('get')
            ->with('api.php', ['query' => $requestParams, 'debug' => $debug])
            ->once()
            ->andReturn($clientResponseMock);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $result = $client->sendGetRequest($requestParams);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers ::sendGetRequest
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @expectedExceptionMessageRegExp /^Unexpected exception occurred\./
     */
    public function testSendGetRequestAndParseWrongResponse()
    {
        $requestParams = ['param1' => 'value1'];
        $debug = false;

        /** @var Mock|ResponseInterface $clientResponseMock */
        $clientResponseMock = Mockery::mock(ResponseInterface::class);
        $clientResponseMock->shouldReceive('getBody')->once()->andReturn([]);

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('get')
            ->with('api.php', ['query' => $requestParams, 'debug' => $debug])
            ->once()
            ->andReturn($clientResponseMock);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $client->sendGetRequest($requestParams);
    }

    /**
     * @covers ::sendGetRequest
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @expectedExceptionMessageRegExp /^Can not send GET request\./
     */
    public function testSendGetRequestAndParseConnectorException()
    {
        $requestParams = ['param1' => 'value1'];
        $debug = false;

        /** @var Mock|RequestInterface $requestInterface */
        $requestInterface = Mockery::mock(RequestInterface::class);
        $exception = new ClientException('Client Exception', $requestInterface);

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('get')
            ->with('api.php', ['query' => $requestParams, 'debug' => $debug])
            ->once()
            ->andThrow($exception);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $client->sendGetRequest($requestParams);
    }

    /**
     * @covers ::sendPostRequest
     */
    public function testSendPostRequest()
    {
        $requestParams = ['param1' => 'value1'];
        $expectedResult = ['paramX' => 'valueY'];
        $debug = false;

        /** @var Mock|ResponseInterface $clientResponseMock */
        $clientResponseMock = Mockery::mock(ResponseInterface::class);
        $clientResponseMock->shouldReceive('getBody')->once()->andReturn(json_encode($expectedResult));

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('post')
            ->with('api.php', ['form_params' => $requestParams, 'debug' => $debug])
            ->once()
            ->andReturn($clientResponseMock);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $result = $client->sendPostRequest($requestParams);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers ::sendPostRequest
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @expectedExceptionMessageRegExp /^Unexpected exception occurred\./
     */
    public function testSendPostRequestAndParseWrongResponse()
    {
        $requestParams = ['param1' => 'value1'];
        $debug = false;

        /** @var Mock|ResponseInterface $clientResponseMock */
        $clientResponseMock = Mockery::mock(ResponseInterface::class);
        $clientResponseMock->shouldReceive('getBody')->once()->andReturn([]);

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('post')
            ->with('api.php', ['form_params' => $requestParams, 'debug' => $debug])
            ->once()
            ->andReturn($clientResponseMock);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $client->sendPostRequest($requestParams);
    }

    /**
     * @covers ::sendPostRequest
     * @expectedException \Signes\vBApi\Exception\VBApiException
     * @expectedExceptionMessageRegExp /^Can not send POST request\./
     */
    public function testSendPostRequestAndParseConnectorException()
    {
        $requestParams = ['param1' => 'value1'];
        $debug = false;

        /** @var Mock|RequestInterface $requestInterface */
        $requestInterface = Mockery::mock(RequestInterface::class);
        $exception = new ClientException('Client Exception', $requestInterface);

        /** @var Mock|CLient $clientMock */
        $clientMock = Mockery::mock(Client::class);
        $clientMock->shouldReceive('post')
            ->with('api.php', ['form_params' => $requestParams, 'debug' => $debug])
            ->once()
            ->andThrow($exception);

        $client = new GuzzleProvider('http://example.com/', $debug);
        $client->setGuzzleClient($clientMock);

        $client->sendPostRequest($requestParams);
    }
}
