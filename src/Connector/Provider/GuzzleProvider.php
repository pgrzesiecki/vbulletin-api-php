<?php

namespace Signes\vBApi\Connector\Provider;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Exception\VBApiException;

/**
 * Class GuzzleProvider.
 *
 * @package Signes\vBApi\Connector\Provider
 */
class GuzzleProvider implements ConnectorInterface
{
    /**
     * vBulletin API url without trailing slash.
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * Guzzle debug.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * GuzzleProvider constructor.
     *
     * @param string $apiUrl
     * @param bool $debug
     */
    public function __construct($apiUrl, $debug = false)
    {
        $this->apiUrl = $apiUrl;
        $this->debug = (bool) $debug;

        $this->initGuzzleClientInstance();
    }

    /**
     * Initialize Guzzle connection
     */
    protected function initGuzzleClientInstance()
    {
        $this->setGuzzleClient(new Client(['base_uri' => $this->apiUrl]));
    }

    /**
     * @param Client $guzzleClient
     * @return static
     */
    public function setGuzzleClient(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;

        return $this;
    }

    /**
     * @param array $params
     * @return array
     * @throws VBApiException
     */
    public function sendGetRequest(array $params)
    {
        try {
            $response = $this->guzzleClient->get(
                'api.php',
                [
                    'query' => $params,
                    'debug' => $this->debug,
                ]
            );
            $result = (array) json_decode($response->getBody());

            return $result;
        } catch (ClientException $e) {
            throw new VBApiException("Can not send GET request. {$e->getMessage()}", $e->getCode(), $e);
        } catch (Exception $e) {
            throw new VBApiException("Unexpected exception occurred. {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * @param array $params
     * @return array
     * @throws VBApiException
     */
    public function sendPostRequest(array $params)
    {
        try {
            $response = $this->guzzleClient->post(
                'api.php',
                [
                    'form_params' => $params,
                    'debug'       => $this->debug,
                ]
            );
            $result = (array) json_decode($response->getBody());

            return $result;
        } catch (ClientException $e) {
            throw new VBApiException("Can not send POST request. {$e->getMessage()}", $e->getCode(), $e);
        } catch (Exception $e) {
            throw new VBApiException("Unexpected exception occurred. {$e->getMessage()}", $e->getCode(), $e);
        }
    }
}
