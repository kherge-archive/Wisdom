<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace CODEAlchemy\Wisdom;

use ArrayObject;
use CODEAlchemy\Wisdom\Loader\INI;
use CODEAlchemy\Wisdom\Loader\YAML;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class WisdomTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $wisdom = new Wisdom(__DIR__);
        $locator = $this->getProperty($wisdom, 'locator');

        $this->assertEquals(array(__DIR__), $this->getProperty($locator, 'paths'));
    }

    public function testAddPath()
    {
        $wisdom = new Wisdom;

        $wisdom->addPath(__DIR__);

        $locator = $this->getProperty($wisdom, 'locator');

        $this->assertEquals(array(__DIR__), $this->getProperty($locator, 'paths'));
    }

    public function testAddLoader()
    {
        $wisdom = new Wisdom;
        $loader = new INI;

        $wisdom->addLoader($loader);

        $this->assertSame($this->getProperty($wisdom, 'locator'), $this->getProperty($wisdom, 'locator'));
        $this->assertSame(array($loader), $this->getProperty($this->getProperty($wisdom, 'resolver'), 'loaders'));
    }

    public function testSetCache()
    {
        $wisdom = new Wisdom;

        $wisdom->setCache(__DIR__);

        $this->assertEquals(__DIR__, $this->getProperty($wisdom, 'cache'));
    }

    public function testSetDebug()
    {
        $wisdom = new Wisdom;

        $wisdom->setDebug(true);

        $this->assertTrue($this->getProperty($wisdom, 'debug'));
    }

    public function testSetPrefix()
    {
        $wisdom = new Wisdom;

        $wisdom->setPrefix('test.');

        $this->assertEquals('test.', $this->getProperty($wisdom, 'prefix'));
    }

    public function testSetValues()
    {
        $wisdom = new Wisdom;
        $array = array('rand' => rand());

        $wisdom->setValues($array);

        $this->assertSame($array, $this->getProperty($wisdom, 'values'));

        $object = new ArrayObject(array('rand' => rand()));

        $wisdom->setValues($object);

        $this->assertSame($object, $this->getProperty($wisdom, 'values'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value of $values is not an array or an instance of ArrayAccess.
     */
    public function testSetValuesBadType()
    {
        $wisdom = new Wisdom;

        $wisdom->setValues(true);
    }

    /**
     * @depends testAddLoader
     * @depends testSetCache
     */
    public function testGet()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/test.yml', <<<YAML
rand: %rand%
YAML
        );

        $wisdom = new Wisdom($dir);

        $wisdom->addLoader(new YAML);
        $wisdom->setCache($dir);
        $wisdom->setPrefix('test.');
        $wisdom->setValues(array('rand' => $rand = rand()));

        $this->assertSame(array('rand' => $rand), $wisdom->get('test.yml'));
        $this->assertSame(array('rand' => $rand), $wisdom->get('test.yml'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value of $values is not an array or an instance of ArrayAccess.
     */
    public function testGetBadValues()
    {
        $wisdom = new Wisdom;

        $wisdom->get(null, true);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The file "./test.yml" does not exist (in: ).
     */
    public function testGetBadFile()
    {
        $wisdom = new Wisdom;

        $wisdom->get('test.yml');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The file "./test.test.yml" does not exist (in: ).
     */
    public function testGetBadFileNested()
    {
        $wisdom = new Wisdom;

        $wisdom->setPrefix('test.');

        $wisdom->get('test.yml');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No loader available for file: test.yml
     */
    public function testGetMissingLoader()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/test.yml', <<<YAML
rand: %rand%
YAML
        );

        $wisdom = new Wisdom($dir);

        $wisdom->get('test.yml');
    }

    private function getProperty($object, $name)
    {
        $class = new ReflectionClass($object);

        while (false === $class->hasProperty($name)) {
            if (null === ($class = $class->getParentClass())) {
                return;
            }
        }

        $property = $class->getProperty($name);

        $property->setAccessible(true);

        return $property->getValue($object);
    }
}

