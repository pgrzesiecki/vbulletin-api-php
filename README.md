# vBulletin 5 API connector

[![Latest Stable Version](https://poser.pugx.org/signes/vbulletin-api-php/v/stable)](https://packagist.org/packages/signes/vbulletin-api-php)
[![Build Status](https://travis-ci.org/signes-pl/vbulletin-api-php.svg?branch=master)](https://travis-ci.org/signes-pl/vbulletin-api-php)
[![Circle CI](https://circleci.com/gh/signes-pl/vbulletin-api-php.svg?style=svg)](https://circleci.com/gh/signes-pl/vbulletin-api-php)
[![codecov.io](https://codecov.io/github/signes-pl/vbulletin-api-php/coverage.svg?branch=master)](https://codecov.io/github/signes-pl/vbulletin-api-php?branch=master)
[![License](https://poser.pugx.org/signes/acl/license)](https://packagist.org/packages/signes/vbulletin-api-php)

1. [What is this?](#what-is-this)
2. [Basic usage](#basic-usage)
3. [Contexts](#contexts)
    * [Available contexts](#available-contexts)
4. [API service](#api-service)
    * [Lazy connection](#lazy-connection)
    * [Remember configuration and connection](#remember-configuration-and-connection)

## What is this?

This package gives you possibility to easy integrate your system with vBulletin 5 API using simple methods calls.

**NOTE. This package is still in alpha version. I do not suggest to use it on production until stable version release.**

## Basic usage

```php
    use Signes\vBApi\Api;
    use Signes\vBApi\ApiConfig;
    use Signes\vBApi\Connector\Provider\GuzzleProvider;
    
    $apiConfig = new ApiConfig($apiKey, $uniqueId, $clientName, $clientVersion, $platformName, $platformVersion);
    $apiConnector = new GuzzleProvider('http://example.com/my-forum/');
 
    $api = new Api($apiConfig, $apiConnector);
    
    $response = $api->callRequest('user.fetchByEmail', ['email' => 'test@example.com']);
```

1. First of all create your API configuration file which should include information's like:

    * **vBulletin API key** - you can find it in vBulletin control panel, under API section. |
    * **Unique ID** - Unique id is used to identity your client and platform name, it can be any unique string. Be careful, if you will change unique ID your request will be recognized as request from new API client and new API client ID will be returned. |
    * **Client name** -  Your client name.
    * **Client version** - Your client version.
    * **Platform name** - Your platform name.
    * **Platform version** - Your platform version.
        
    
    ```php
    use Signes\vBApi\ApiConfig;
    $apiConfig = new ApiConfig($apiKey, $uniqueId, $clientName, $clientVersion, $platformName, $platformVersion);
    ```

2. When configuration object is ready, next step is to initiate connection provider. This provider is responsible for communication between your application and vB forum.
    
    By default you can use included **Guzzle** provider but before you will do this, you should require **guzzlehttp/guzzle** package in composer. Guzzle provider required forum URL in constructor with trailing slash. 
       
    ```php
    use Signes\vBApi\Connector\Provider\GuzzleProvider;
    $apiConnector = new GuzzleProvider('http://example.com/my-forum/');
    ```
        
    If this is not good enough, you can provide own connection class, just implement `Signes\vBApi\Connector\ConnectorInterface` interface.
        
3. After that you are ready to prepare API service, which required `Signes\vBApi\ApiConfig` and `Signes\vBApi\Connector\ConnectorInterface` objects.
    
    ```php
    use Signes\vBApi\Api;
    $api = new Api($apiConfig, $apiConnector);
    ```
        
4. When API service is initialized, you can call any API request.
    
    ```php
    $response = $api->callRequest('user.fetchByEmail', ['email' => 'test@example.com']);
    $response = $api->callRequest('site.getSiteStatistics', []);
    ```
        
## Contexts

Contexts gives you possibility to encapsulation logic into single class. Under the hood contexts also uses raw vB API requests.   
Every context must extend `Signes\vBApi\Context\Context` class and can required different parameters in constructor.

Example code:

```php
    use Signes\vBApi\Api;
    use Signes\vBApi\ApiConfig;
    use Signes\vBApi\Connector\Provider\GuzzleProvider;
    use Signes\vBApi\Context\User\FetchCurrentUserInfo;
    use Signes\vBApi\Context\User\Login;
    
    $apiConfig = new ApiConfig($apiKey, $uniqueId, $clientName, $clientVersion, $platformName, $platformVersion);
    $apiConnector = new GuzzleProvider('http://example.com/my-forum/');
    $api = new Api($apiConfig, $apiConnector);
    
    $currentLoggedInUserContext = new FetchCurrentUserInfo();
    $loginUserContext = new Login('adminUsername', 'adminPassword');
    
    $meFirst = $api->callContextRequest($currentLoggedInUserContext);
    $api->callContextRequest($loginUserContext);
    $meSecond = $api->callContextRequest($currentLoggedInUserContext);
    
    echo $meFirst['username'] // "Guest"
    echo $meSecond['username'] // "adminUsername"
```

Context should handle information about request method type, API request type and should return all required parameters by vB for given request.   
For your custom context you can also overwrite `parseResponse` method to return expected format, not raw vB API response.

### Available contexts

* User
    * Login
    * Fetch current logged in user data
    * Fetch user data by email
        
* more contexts soon ...
        
## API service

What do API service for you:

* send `init` request to retrieve access token, secret, api version and client id for future requests,
* remember your identity (when you send valid login request, every future requests will be called as this logged in user),
* prepare signature for every request and include every required by vBulletin parameters,

If you want to integrate with multiple vBulletin instances, you can use API service as a registry. If instance is not remembered `null` will be returned.

```php
use Signes\vBApi\Api;
(new Api($apiConfig, $apiConnector))->rememberInstance('myInstanceName');
(new Api($apiConfigSecond, $apiConnectorSecond))->rememberInstance('myOtherInstance');

$firstInstance = Api::getInstance('myInstanceName');
$secondInstance = Api::getInstance('myOtherInstance');
```

### Lazy connection

API service constructor has third parameter `$lazy` with default **true** value. This parameter determine moment of `init` request. 

If it is set to **true** `init` request will be send during the first `callRequest` method call. So if you provided incorrect configuration you will learn about it until now. 

If it is set to **false** `init` request will be send when API service instance is created
   
   
### Remember configuration and connection

It is possible to keep information about `ApiConfig` between PHP requests and it is strongly recommend to do that on production. To do that you can use any of your cache services. Just store `ApiConfig` in cache, and for future use it instance.

That gives you one more benefit, when you login on user account, this information will be remembered in `ApiConfig` and you do not have to call login request for some period of time (depends of your cache expiration).

##  Credits

Pawel Grzesiecki - Developer (http://signes.pl/) [MIT](https://opensource.org/licenses/MIT) License