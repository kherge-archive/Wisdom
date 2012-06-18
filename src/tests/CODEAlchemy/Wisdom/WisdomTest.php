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

    use CODEAlchemy\Wisdom\Config\FileLocator,
        CODEAlchemy\Wisdom\Loader\INI,
        CODEAlchemy\Wisdom\Loader\JSON,
        CODEAlchemy\Wisdom\Loader\YAML,
        PHPUnit_Framework_TestCase,
        Symfony\Component\Config\Loader\DelegatingLoader,
        Symfony\Component\Config\Loader\LoaderResolver;

    class WisdomTest extends PHPUnit_Framework_TestCase
    {
        private $wisdom;

        protected function setUp()
        {
            $this->wisdom = new Wisdom;
        }

        public function testGetCachePath()
        {
            $this->assertSame('', $this->wisdom->getCachePath());
        }

        public function testGetPaths()
        {
            $this->assertSame(
                array(),
                $this->wisdom->getPaths()
            );
        }

        public function testGetLoaders()
        {
            $this->assertSame(
                array(),
                $this->wisdom->getLoaders()
            );
        }

        public function testGetPrefix()
        {
            $this->assertSame('', $this->wisdom->getFilePrefix());
        }

        public function testGetReplacementValues()
        {
            $this->assertNull($this->wisdom->getReplacementValues());
        }

        public function testIsCache()
        {
            $this->assertFalse($this->wisdom->isCache());
        }

        public function testIsDebug()
        {
            $this->assertFalse($this->wisdom->isDebug());
        }

        /**
         * @depends testGetLoaders
         * @depends testGetPaths
         * @depends testIsDebug
         */
        public function testConstructor()
        {
            $paths = __DIR__;

            $loaders = array(
                new INI(new FileLocator),
                new JSON(new FileLocator)
            );

            $this->wisdom = new Wisdom($paths, $loaders);

            $this->assertSame(array($paths), $this->wisdom->getPaths());
            $this->assertSame($loaders, $this->wisdom->getLoaders());
        }

        /**
         * @depends testGetLoaders
         */
        public function testAddLoader()
        {
            $loader = new INI($this->wisdom->getLocator());

            $this->wisdom->addLoader($loader);

            $this->assertSame(array($loader), $this->wisdom->getLoaders());
        }

        /**
         * @depends testGetPaths
         */
        public function testAddPath()
        {
            $this->wisdom->addPath(__DIR__);

            $this->assertSame(array(__DIR__), $this->wisdom->getPaths());
        }

        /**
         * @depends testGetCachePath
         * @depends testIsCache
         */
        public function testSetCachePath()
        {
            $this->wisdom->setCachePath(__DIR__);

            $this->assertEquals(__DIR__, $this->wisdom->getCachePath());
            $this->assertTrue($this->wisdom->isCache());
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage The cache path is not a directory:
         */
        public function testSetCachePathInvalid()
        {
            $this->wisdom->setCachePath(tempnam(sys_get_temp_dir(), 'wis'));
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage Unable to create cache directory:
         */
        public function testSetCachePathFail()
        {
            @ $this->wisdom->setCachePath(tempnam(sys_get_temp_dir(), 'wis') . '/test');
        }

        /**
         * @depends testIsDebug
         */
        public function testSetDebug()
        {
            $this->wisdom->setDebug(true);

            $this->assertTrue($this->wisdom->isDebug());
        }

        /**
         * @depends testGetPrefix
         */
        public function testSetPrefix()
        {
            $this->wisdom->setFilePrefix('test.');

            $this->assertEquals('test.', $this->wisdom->getFilePrefix());
        }

        /**
         * @depends testGetReplacementValues
         */
        public function testSetReplacementValues()
        {
            $this->wisdom->setReplacementValues($values = array(rand()));

            $this->assertSame($values, $this->wisdom->getReplacementValues());
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage The $values argument is not an array or implements ArrayAccess.
         */
        public function testSetReplacementValuesInvalid()
        {
            $this->wisdom->setReplacementValues('test');
        }

        /**
         * @depends testIsCache
         * @depends testSetCachePath
         * @depends testSetDebug
         */
        public function testGet()
        {
            unlink($file = tempnam(sys_get_temp_dir(), 'wis'));

            $data = array(
                'category' => array(
                    'test' => 'My value.',
                    'another' => array(
                        'One',
                        'Two',
                        'Three'
                    )
                ),
                'rand' => rand()
            );

            file_put_contents(
                $file .= '.json',
                utf8_encode(json_encode($data))
            );

            $this->wisdom->addPath(dirname($file));
            $this->wisdom->addLoader(new JSON ($this->wisdom->getLocator()));
            $this->wisdom->setCachePath(sys_get_temp_dir());
            $this->wisdom->setFilePrefix('test.');

            $this->assertSame(array($data, $file), $this->wisdom->get(basename($file), true, array()));

            unlink($file . '.php');

            $this->assertSame($data, $this->wisdom->get(basename($file)));
            $this->assertSame($data, $this->wisdom->get(basename($file)));
            $this->assertSame(array($data, $file), $this->wisdom->get(basename($file), true, array()));
            $this->assertTrue(file_exists($file . '.php'));

            unlink($file);
        }

        /**
         * @depends testGet
         */
        public function testGetWithPrefix()
        {
            unlink($file = tempnam(sys_get_temp_dir(), 'wis'));

            $file = dirname($file) . '/test.' . basename($file);
            $data = array(
                'category' => array(
                    'test' => 'My value.',
                    'another' => array(
                        'One',
                        'Two',
                        'Three'
                    )
                ),
                'rand' => rand()
            );

            file_put_contents(
                $file .= '.json',
                utf8_encode(json_encode($data))
            );

            $this->wisdom->addPath(dirname($file));
            $this->wisdom->addLoader(new JSON ($this->wisdom->getLocator()));
            $this->wisdom->setCachePath(sys_get_temp_dir());
            $this->wisdom->setFilePrefix('test.');

            $this->assertSame(array($data, $file), $this->wisdom->get(basename($file), true, array()));
            $this->assertTrue(file_exists($file . '.php'));

            unlink($file);
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage The file "test.test.yml" does not exist (in: ).
         */
        public function testGetNotExist()
        {
            $this->wisdom->setFilePrefix('test.');

            $this->wisdom->get('test.yml');
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage The $values argument is not an array or implements ArrayAccess.
         */
        public function testGetInvalidValues()
        {
            $this->wisdom->get(null, null, 'test');
        }

        /**
         * @expectedException RuntimeException
         * @expectedExceptionMessage No available loader for file:
         */
        public function testGetNoLoader()
        {
            unlink($file = tempnam(sys_get_temp_dir(), 'wis'));

            touch($file .= '.json');

            $this->wisdom->get($file);
        }
    }