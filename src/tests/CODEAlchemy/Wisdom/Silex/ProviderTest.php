<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Silex;

    use PHPUnit_Framework_TestCase,
        Silex\Application;

    class ProviderTest extends PHPUnit_Framework_TestCase
    {
        private $app;
        private $provider;

        public function setUp()
        {
            $this->app = new Application;

            $this->provider = new Provider;
        }

        public function testImplementation()
        {
            $this->assertInstanceOf(
                'Silex\ServiceProviderInterface',
                $this->provider
            );
        }

        public function testBoot()
        {
            $this->assertNull($this->provider->boot($this->app));
        }

        public function testRegister()
        {
            $this->app->register($this->provider, array(
                'wisdom.path' => __DIR__
            ));

            $this->assertSame(array(), $this->app['wisdom.options']);

            $locator = $this->app['wisdom']->getLocator();

            $this->assertSame(
                array(
                    'cache_path' => '',
                    'debug' => $this->app['debug'],
                    'loader' => array(
                        'CODEAlchemy\Wisdom\Loader\INI',
                        'CODEAlchemy\Wisdom\Loader\JSON',
                        'CODEAlchemy\Wisdom\Loader\YAML'
                    ),
                    'prefix' => 'prod.'
                ),
                $this->app['wisdom.options']
            );

            $this->assertEquals(
                array(__DIR__),
                $locator->getPaths()
            );

            $loaders = $this->app['wisdom']->getLoaders();

            $this->assertInstanceOf('CODEAlchemy\Wisdom\Loader\INI', $loaders[0]);
            $this->assertInstanceOf('CODEAlchemy\Wisdom\Loader\JSON', $loaders[1]);
            $this->assertInstanceOf('CODEAlchemy\Wisdom\Loader\YAML', $loaders[2]);
            $this->assertSame($locator, $loaders[0]->getLocator());
            $this->assertSame($locator, $loaders[1]->getLocator());
            $this->assertSame($locator, $loaders[2]->getLocator());

            $this->assertSame($locator, $this->app['wisdom']->getLocator());
            $this->assertSame($this->app['wisdom.options']['cache_path'], $this->app['wisdom']->getCachePath());
            $this->assertSame($this->app['wisdom.options']['debug'], $this->app['wisdom']->isDebug());
            $this->assertEquals($this->app['wisdom.options']['prefix'], $this->app['wisdom']->getFilePrefix());
        }

        /**
         * @depends testRegister
         */
        public function testUse()
        {
            unlink($dir = tempnam(sys_get_temp_dir(), 'wis'));

            mkdir($dir);

            file_put_contents($dir . '/test.ini', <<<INI
my = "value"
INI
            );

            $this->app->register($this->provider, array(
                'wisdom.path' => $dir,
                'wisdom.options' => array(
                    'cache_path' => $dir
                )
            ));

            $this->app['wisdom']->setReplacementValues(null);

            $settings = $this->app['wisdom']->get('test.ini');

            $this->assertEquals('value', $settings['my']);
        }
    }