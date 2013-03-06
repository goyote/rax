<?php

use Rax\Mvc\ServerMode;

/**
 * Tests for the ServerMode class.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class ServerModeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function setIntProvider()
    {
        return array(
            array(ServerMode::PRODUCTION, ServerMode::PRODUCTION, 'production', 'prod'),
            array(ServerMode::STAGING, ServerMode::STAGING, 'staging', 'prod'),
            array(ServerMode::TESTING, ServerMode::TESTING, 'testing', 'dev'),
            array(ServerMode::DEVELOPMENT, ServerMode::DEVELOPMENT, 'development', 'dev'),

            array('production', ServerMode::PRODUCTION, 'production', 'prod'),
            array('staging', ServerMode::STAGING, 'staging', 'prod'),
            array('testing', ServerMode::TESTING, 'testing', 'dev'),
            array('development', ServerMode::DEVELOPMENT, 'development', 'dev'),
        );
    }

    /**
     * @dataProvider setIntProvider
     *
     * @param string $env
     * @param int    $int
     * @param string $name
     * @param string $shortName
     */
    public function testSetInt($env, $int, $name, $shortName)
    {
        ServerMode::set($env);
        $this->assertSame($int, ServerMode::get());
        $this->assertSame($name, ServerMode::getName());
        $this->assertSame($shortName, ServerMode::getShortName());
    }

    /**
     * @return array
     */
    public function setExceptionProvider()
    {
        return array(
            array($this),
            array(9.9),
            array(array()),
            array(null),
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider setExceptionProvider
     * @expectedException InvalidArgumentException
     *
     * @param mixed $env
     */
    public function testSetException($env)
    {
        ServerMode::set($env);
    }

    /**
     * @expectedException Exception
     */
    public function testGetNameException()
    {
        ServerMode::set(34);
        ServerMode::getName();
    }

    /**
     * @expectedException Exception
     */
    public function testGetShortNameException()
    {
        ServerMode::set(500);
        ServerMode::getShortName();
    }

    /**
     * @return array
     */
    public function isMethodsProvider()
    {
        return array(
            array(ServerMode::PRODUCTION, 'isProduction'),
            array(ServerMode::STAGING, 'isStaging'),
            array(ServerMode::TESTING, 'isTesting'),
            array(ServerMode::DEVELOPMENT, 'isDevelopment'),
        );
    }

    /**
     * @dataProvider isMethodsProvider
     *
     * @param string $env
     * @param string $method
     */
    public function testIsMethods($env, $method)
    {
        ServerMode::set($env);
        $this->assertTrue(ServerMode::$method());
    }

    /**
     * @return array
     */
    public function isProvider()
    {
        return array(
            array(true, ServerMode::DEVELOPMENT),
            array(true, 'development'),

            array(false, ServerMode::PRODUCTION),
            array(false, 'production'),
            array(false, $this),
            array(false, 9.9),
            array(false, array()),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider isProvider
     *
     * @param bool  $bool
     * @param mixed $argument
     */
    public function testIs($bool, $argument)
    {
        ServerMode::set(ServerMode::DEVELOPMENT);
        $this->assertSame($bool, ServerMode::is($argument));
    }
}
