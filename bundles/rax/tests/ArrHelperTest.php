<?php
use Rax\Helper\Arr;
use Rax\Mvc\Exception;

/**
 * Tests for the ArrHelper class.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class ArrHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function isArrayProvider()
    {
        return array(
            array(true, array()),
            array(true, new ArrayObject()),

            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider isArrayProvider
     *
     * @param bool  $bool
     * @param mixed $argument
     */
    public function testIsArray($bool, $argument)
    {
        $this->assertSame($bool, Arr::isArray($argument));
    }

    /**
     * @return array
     */
    public function isAssociativeProvider()
    {
        return array(
            array(true, array('a' => 'b')),
            array(true, new ArrayObject(array('a' => 'b'))),

            array(false, array()),
            array(false, array('a')),
            array(false, new ArrayObject(array('a'))),
            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider isAssociativeProvider
     *
     * @param bool  $bool
     * @param mixed $argument
     */
    public function testIsAssociative($bool, $argument)
    {
        $this->assertSame($bool, Arr::isAssociative($argument));
    }

    /**
     * @return array
     */
    public function isNumericProvider()
    {
        return array(
            array(true, array('a')),
            array(true, new ArrayObject(array('a'))),

            array(false, array()),
            array(false, array('a' => 'b')),
            array(false, new ArrayObject(array('a' => 'b'))),
            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider isNumericProvider
     *
     * @param bool  $bool
     * @param mixed $argument
     */
    public function testIsNumeric($bool, $argument)
    {
        $this->assertSame($bool, Arr::isNumeric($argument));
    }

    /**
     * @return array
     */
    public function unshiftProvider()
    {
        return array(
            array(array('a' => 'b', 'c' => 'd'), array('c' => 'd'), 'a', 'b'),
            array(array('a' => 'c'), array('a' => 'b'), 'a', 'c'),
        );
    }

    /**
     * @dataProvider unshiftProvider
     *
     * @param array  $expected
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     */
    public function testUnshift($expected, $array, $key, $value)
    {
        $this->assertSame($expected, Arr::unshift($array, $key, $value));
    }

    /**
     * @return array
     */
    public function setProvider()
    {
        return array(
            array(array('a' => 'b'), array(), 'a', 'b', null),
            array(array('a' => 'c'), array('a' => 'b'), 'a', 'c', null),
            array(new ArrayObject(array('a' => 'c')), new ArrayObject(array('a' => 'b')), 'a', 'c', null),
            array(array('a' => array('b' => 'c')), array('a' => 'b'), 'a', array('b' => 'c'), null),
            array(array('a' => array('b' => 'd')), array('a' => array('b' => 'c')), 'a.b', 'd', null),
            array(array('a' => array('b' => array('b' => 2, 'd' => false, 'c' => 'd'))), array('a' => array('b' => array('c' => 'd'))), array('a.b.b' => 2, 'a.b.d' => false), null, null),
            array(array('a' => array('b' => array('c' => 'd'))), array(), 'a.b.c', 'd', null),
        );
    }

    /**
     * @dataProvider setProvider
     *
     * @param array|ArrayAccess $expected
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $value
     * @param string            $delimiter
     */
    public function testSet($expected, $array, $key, $value, $delimiter)
    {
        Arr::set($array, $key, $value, $delimiter);
        $this->assertEquals($expected, $array);
    }

    /**
     * @return array
     */
    public function setExceptionProvider()
    {
        return array(
            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider setExceptionProvider
     * @expectedException Rax\Mvc\Exception
     *
     * @param mixed $argument
     */
    public function testSetException($argument)
    {
        Arr::set($argument, null, null);
    }

    /**
     * @return array
     */
    public function getExceptionProvider()
    {
        return array(
            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider getExceptionProvider
     * @expectedException Rax\Mvc\Exception
     *
     * @param mixed $argument
     */
    public function testGetException($argument)
    {
        Arr::get($argument, null, null, null);
    }

    /**
     * @return array
     */
    public function hasExceptionProvider()
    {
        return array(
            array(false, $this),
            array(false, function() {}),
            array(false, ''),
            array(false, 9),
            array(false, 9.9),
            array(false, null),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider hasExceptionProvider
     * @expectedException Rax\Mvc\Exception
     *
     * @param mixed $argument
     */
    public function testHasException($argument)
    {
        Arr::has($argument, null, null);
    }

    /**
     * @return array
     */
    public function getProvider()
    {
        return array(
            array('d', array('a' => 'b', 'c' => 'd'), 'c', null, null),
            array(array('c' => 'd'), array('a' => array('b' => array('c' => 'd'))), 'a.b', null, null),
            array(array('a' => array('b' => 'c')), array('a' => array('b' => 'c')), null, null, null),
            array(array('a' => array('b' => 'c'), 'a.b' => 'c'), array('a' => array('b' => 'c')), array('a', 'a.b'), null, null),
            array(3, array('a' => 'b', 'c' => 'd'), 'd', 3, null),
            array(3, array('a' => 'b', 'c' => 'd'), 'd', function() {return 3;}, null),
            array(array('a' => array('b' => 'c'), 'a.b' => 'c'), new ArrayObject(array('a' => array('b' => 'c'))), array('a', 'a.b'), null, null),
        );
    }

    /**
     * @dataProvider getProvider
     *
     * @param array|ArrayAccess $expected
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $default
     * @param string            $delimiter
     */
    public function testGet($expected, $array, $key, $default, $delimiter)
    {
        $this->assertEquals($expected, Arr::get($array, $key, $default, $delimiter));
    }

    /**
     * @return array
     */
    public function deleteProvider()
    {
        return array(
            array(array('a' => 'b'), array('a' => 'b', 'c' => 'd'), 'c', null),
            array(array('a' => 'b'), array('a' => 'b', 'c' => 'd', 'e' => 'f'), array('c', 'e'), null),
            array(array('a' => array('b' => array())), array('a' => array('b' => array('c' => 'e'))), 'a.b.c', null),
        );
    }

    /**
     * @dataProvider deleteProvider
     *
     * @param array|ArrayAccess $expected
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     */
    public function testDelete($expected, $array, $key, $delimiter)
    {
        Arr::delete($array, $key, $delimiter);
        $this->assertEquals($expected, $array);
    }

    /**
     * @return array
     */
    public function deleteReturnProvider()
    {
        return array(
            array(true, array('a' => 'b'), 'a', null),
            array(false, array('a' => 'b'), 'b', null),
            array(false, array('a' => 'b'), 'a.c', null),
        );
    }

    /**
     * @dataProvider deleteReturnProvider
     *
     * @param array|ArrayAccess $bool
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     */
    public function testDeleteReturn($bool, $array, $key, $delimiter)
    {
        $this->assertEquals($bool, Arr::delete($array, $key, $delimiter));
    }

    /**
     * @return array
     */
    public function hasProvider()
    {
        return array(
            array(true, array('a' => 'b', 'c' => 'd'), 'c', null),
            array(true, array('a' => array('b' => 'c')), array('a', 'a.b'), null),
            array(false, array('a' => array('b' => 'c')), array('a', 'a.c'), null),
            array(false, array('a' => 'b', 'c' => 'd'), 'd', null),
        );
    }

    /**
     * @dataProvider hasProvider
     *
     * @param array|ArrayAccess $bool
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     */
    public function testHas($bool, $array, $key, $delimiter)
    {
        $this->assertEquals($bool, Arr::has($array, $key, $delimiter));
    }
}
