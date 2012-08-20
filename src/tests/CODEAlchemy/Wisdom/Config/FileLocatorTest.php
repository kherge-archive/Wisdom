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
use ReflectionProperty;

class FileLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testAddPath()
    {
        $locator = new FileLocator;

        $locator->addPath(__DIR__);

        $paths = new ReflectionProperty($locator, 'paths');

        $paths->setAccessible(true);

        $this->assertEquals(array(__DIR__), $paths->getValue($locator));
    }
}

