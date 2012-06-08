<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Config;

    use PHPUnit_Framework_TestCase;

    class FileLocatorTest extends PHPUnit_Framework_TestCase
    {
        public function testGetPaths()
        {
            $locator = new FileLocator(__DIR__);

            $this->assertSame(array(__DIR__), $locator->getPaths());
        }

        /**
         * @depends testGetPaths
         */
        public function testAddPaths()
        {
            $locator = new FileLocator(__DIR__);

            $locator->addPath('/test/path');

            $this->assertSame(
                array(
                    __DIR__,
                    '/test/path'
                ),
                $locator->getPaths()
            );
        }
    }