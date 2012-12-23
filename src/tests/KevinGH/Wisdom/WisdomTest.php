<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom;

use ArrayObject;
use KevinGH\Wisdom\Loader\INI;
use KevinGH\Wisdom\Loader\YAML;
use NonTraversable;
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

        $this->assertSame(
            $this->getProperty($wisdom, 'locator'),
            $this->getProperty($wisdom, 'locator')
        );
        $this->assertSame(
            array($loader),
            $this->getProperty(
                $this->getProperty($wisdom, 'resolver'),
                'loaders'
            )
        );
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
     * @depends testGet
     */
    public function testGetImport()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/a.yml', <<<YAML
imports:
    - b.yml

a_value:
    - 012
YAML
        );

        file_put_contents($dir . '/b.yml', <<<YAML
imports:
    - c.yml

a_value: 345
b_value: 678
YAML
        );

        file_put_contents($dir . '/c.yml', <<<YAML
a_value: 901
c_value: 234
YAML
        );

        $wisdom = new Wisdom($dir);
        $wisdom->addLoader(new YAML());
        $wisdom->setCache($dir);

        $result = $wisdom->get('a.yml');

        $this->assertEquals(array(012), $result['a_value']);
        $this->assertEquals(678, $result['b_value']);
        $this->assertEquals(234, $result['c_value']);
    }

    /**
     * @depends testGet
     */
    public function testGetObject()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/test.yml', <<<YAML
rand: %rand%
YAML
        );

        $wisdom = new Wisdom($dir);
        $a = new ArrayObject();
        $b = new ArrayObject();

        $a['rand'] = $x = rand();
        $b['rand'] = $y = rand();

        $wisdom->addLoader(new YAML);
        $wisdom->setCache($dir);
        $wisdom->setPrefix('test.');
        $wisdom->setValues($a);

        $this->assertSame(array('rand' => $x), $wisdom->get('test.yml'));

        unlink($dir . '/test.yml.php');

        $this->assertSame(array('rand' => $y), $wisdom->get('test.yml', $b));
        $this->assertEquals($x, $a['rand']);
        $this->assertEquals($y, $b['rand']);

        unlink($dir . '/test.yml.php');

        $a = array('rand' => $x);

        $wisdom->setValues($a);

        $this->assertSame(array('rand' => $y), $wisdom->get('test.yml', $b));
        $this->assertEquals($x, $a['rand']);
        $this->assertEquals($y, $b['rand']);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Circular dependency detected for:
     */
    public function testGetCircularDependency()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/a.yml', <<<YAML
imports:
    - b.yml
YAML
        );

        file_put_contents($dir . '/b.yml', <<<YAML
imports:
    - a.yml
YAML
        );

        $wisdom = new Wisdom($dir);
        $wisdom->addLoader(new YAML());
        $wisdom->setCache($dir);

        $result = $wisdom->get('a.yml');
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

