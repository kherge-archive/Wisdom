<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom\Silex;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Silex\Application;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    public function testBoot()
    {
        $app = new Application;
        $provider = new Provider;

        $this->assertNull($provider->boot($app));
    }

    public function testCreateService()
    {
        unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));
        mkdir($dir);

        file_put_contents($dir . '/test.yml', <<<YAML
category:
test: 123
YAML
        );

        $app = new Application;

        Provider::createService($app, 'test', 'test.path', 'test.options');

        $this->assertSame(array(), $app['test.options']);

        $app['test.options'] = array(
            'cache_path' => $dir,
            'prefix' => 'test.'
        );

        $app['test.path'] = $dir;

        $this->assertInstanceOf('KevinGH\Wisdom\Wisdom', $app['test']);
        $this->assertEquals($dir, $this->getProperty($app['test'], 'cache'));
        $this->assertSame($app['debug'], $this->getProperty($app['test'], 'debug'));
        $this->assertEquals('test.', $this->getProperty($app['test'], 'prefix'));
        $this->assertSame($app, $this->getProperty($app['test'], 'values'));

        $loaders = $this->getProperty($this->getProperty($app['test'], 'resolver'), 'loaders');

        $this->assertInstanceOf('KevinGH\Wisdom\Loader\INI', $loaders[0]);
        $this->assertInstanceOf('KevinGH\Wisdom\Loader\JSON', $loaders[1]);
        $this->assertInstanceOf('KevinGH\Wisdom\Loader\YAML', $loaders[2]);
    }

    public function testRegister()
    {
        $app = new Application;
        $provider = new Provider;

        $provider->register($app);

        $this->assertTrue(isset($app['wisdom']));
        $this->assertTrue(isset($app['wisdom.options']));
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

