<?php

namespace Signes\vBApi;

use Exception;
use Signes\vBApi\Connector\ConnectorInterface;
use Signes\vBApi\Context\Context;
use Signes\vBApi\Exception\VBApiException;

/**
 * Class Api
 *
 * @package Signes\vBApi
 */
class Api
{
    /**
     * This is method name which we need to call to initialize connection with vBulletin.
     * This is special method because do not need to sign request.
     */
    const VBULLETIN_API_INIT = 'api.init';

    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @var ApiConfig
     */
    private $config;

    /**
     * @var Api[]
     */
    private static $instances = [];

    /**
     * ApiFactory constructor.
     *
     * @param ApiConfig $config             Api config.
     * @param ConnectorInterface $connector Connector provider.
     * @param bool $lazy                    If false we init API connection immediately and save vBulletin
     *                                      authorization data.
     * @throws VBApiException
     */
    public function __construct(ApiConfig $config, ConnectorInterface $connector, $lazy = true)
    {
        $this->connector = $connector;
        $this->config = $config;

        if ($lazy === false) {
            $this->initApiConnection();
        }
    }

    /**
     * @param string $name    Instance name
     * @param bool $overwrite Can we overwritten existed instance?.
     * @return Api
     * @throws VBApiException
     */
    public function rememberInstance($name, $overwrite = false)
    {
        if (isset(self::$instances[$name]) && $overwrite !== true) {
            throw new VBApiException("Can not overwrite instance of vBApi named '{$name}'");
        }

        self::$instances[$name] = $this;

        return $this;
    }

    /**
     * Return remembered instance of API connection.
     *
     * @param string $name
     * @return Api|null
     */
    public static function getInstance($name)
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        return null;
    }

    /**
     * Call request directly to vBulletin API using connector.
     *
     * @param $apiMethod
     * @param array $params
     * @param string $requestMethod
     * @return mixed
     * @throws VBApiException
     */
    public function callRequest($apiMethod, array $params, $requestMethod = ConnectorInterface::METHOD_POST)
    {
        /**
         * If this is not initialize method and we do not have authorization data yet, authorize connection silently.
         */
        if (!$this->isConnectionInitialized() && $apiMethod !== self::VBULLETIN_API_INIT) {
            $this->initApiConnection();
        }

        /**
         * Arm params using access data and calculate sign for request.
         */
        $params['api_m'] = $apiMethod;

        /**
         * Process request using connector.
         */
        switch ($requestMethod) {
            case ConnectorInterface::METHOD_GET:
                return $this->connector->sendGetRequest(array_merge($this->addApiAuthInfoToParams($params), $params));
                break;
            case ConnectorInterface::METHOD_POST:
                return $this->connector->sendPostRequest(array_merge($this->addApiAuthInfoToParams([]), $params));
                break;
            default:
                throw new VBApiException("Can not call request, unsupported request method provided: {$requestMethod}");
        }
    }

    /**
     * @param Context $context
     * @return mixed
     */
    public function callContextRequest(Context $context)
    {
        return $context->parseResponse(
            $this->callRequest($context->getApiMethod(), $context->getRequestParams(), $context->getRequestMethod())
        );
    }

    /**
     * Initialize connection with vBulletin API.
     * Send 'init' request and save access parameters from response.
     */
    private function initApiConnection()
    {
        $response = $this->callRequest(
            self::VBULLETIN_API_INIT,
            $this->config->getInitRequestParams(),
            ConnectorInterface::METHOD_GET
        );

        try {
            $this->config->setAccessDataFromApiResponse($response);
        } catch (Exception $e) {
            throw new VBApiException("Can not init connection with API because: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Prepare VBulletin signature for given parameters.
     *
     * @param array $params Request params
     * @return string Ready signature
     */
    private function getApiSignatureForParams(array $params)
    {
        $params = $this->filterApiParams($params);
        ksort($params);
        $sign = http_build_query($params, '', '&');
        $sign .= $this->config->getAccessToken();
        $sign .= $this->config->getClientId();
        $sign .= $this->config->getSecret();
        $sign .= $this->config->getApiKey();

        return md5($sign);
    }

    /**
     * Filter request params from attributes which should not be used to calculate API sign.
     *
     * @param array $params
     * @return mixed
     */
    private function filterApiParams(array $params)
    {
        return array_filter(
            $params,
            function ($key) {
                return !in_array($key, ['api_v']);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Add all required by VBulletin request parameters to sign.
     *
     * @see http://www.vbulletin.com/vbcms/content.php/334-mobile-api
     * @param array $params Request params
     * @return array Request params with all required by VBulletin access parameters.
     */
    private function addApiAuthInfoToParams(array $params)
    {
        $response['api_sig'] = $this->getApiSignatureForParams($params);
        $response['api_c'] = $this->config->getClientId();
        $response['api_s'] = $this->config->getAccessToken();
        $response['api_v'] = isset($params['api_v']) ? $params['api_v'] : $this->config->getApiVersion();

        return $response;
    }

    /**
     * Check is connection is initialized and we have correct access token and secret from vB API.
     *
     * @return bool
     */
    private function isConnectionInitialized()
    {
        return $this->config->getAccessToken() && $this->config->getSecret();
    }
}
