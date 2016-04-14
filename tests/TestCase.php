<?php
namespace Signes\vBApi;

use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class TestCase.
 * Every tests from this package should extends this class.
 *
 * @package Signes\vBApi
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Tear down environment after tests.
     */
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @param $object
     * @param $methodName
     * @return mixed
     */
    protected function callPrivateMethod($object, $methodName)
    {
        $reflectionClass = new ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);
        $params = array_slice(func_get_args(), 2);

        return $reflectionMethod->invokeArgs($object, $params);
    }

    /**
     * @param $object
     * @param string $paramName
     * @return mixed
     */
    protected function getPrivateParam($object, $paramName)
    {
        $reflectionProperty = new ReflectionProperty($object, $paramName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
