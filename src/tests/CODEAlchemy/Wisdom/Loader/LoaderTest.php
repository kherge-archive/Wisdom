<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace CODEAlchemy\Wisdom\Loader;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;

define('TEST_CONSTANT_REPLACE', 15);

class _Loader extends Loader
{
    public function load($resource, $type = null)
    {
    }

    public function supports($resource, $type = null)
    {
    }
}

class LoaderTest extends PHPUnit_Framework_TestCase
{
    public function testSetLocator()
    {
        $loader = new _Loader;
        $locator = new FileLocator;

        $loader->setLocator($locator);

        $class = new ReflectionClass($loader);
        $class = $class->getParentClass();
        $property = $class->getProperty('locator');

        $property->setAccessible(true);

        $this->assertSame($locator, $property->getValue($loader));
    }

    public function testSetValues()
    {
        $loader = new _Loader;
        $values = array('rand' => rand());

        $loader->setValues($values);

        $class = new ReflectionClass($loader);
        $class = $class->getParentClass();
        $property = $class->getProperty('values');

        $property->setAccessible(true);

        $this->assertSame($values, $property->getValue($loader));
    }

    /**
     * @depends testSetValues
     */
    public function testDoReplace()
    {
        $loader = new _Loader;

        $loader->setValues(array('rand' => $rand = rand()));

        $input = <<<RAW
raw_data:
broken:
    leftConstant: #left
    leftVariable: %left
    rightConstant: right#
    rightVariable: right%
working:
    constantFirst: #TEST_CONSTANT_REPLACE#
    constantNotSet: #NOT_SET#
    constantSecond: #TEST_CONSTANT_REPLACE#
    variableFirst: %rand%
    variableNotSet: %notSet%
    variableSecond: %rand%
RAW
        ;

        $expected = <<<RAW
raw_data:
broken:
    leftConstant: #left
    leftVariable: %left
    rightConstant: right#
    rightVariable: right%
working:
    constantFirst: 15
    constantNotSet: #NOT_SET#
    constantSecond: 15
    variableFirst: $rand
    variableNotSet: %notSet%
    variableSecond: $rand
RAW
        ;

        $this->assertEquals($expected, $loader->doReplace($input));
    }
}

