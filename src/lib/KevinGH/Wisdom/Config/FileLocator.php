<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom\Config;

use Symfony\Component\Config\FileLocator as Base;

/**
 * Allows paths to be added to FileLocator.
 *
 * @author Kevin Herrera <me@kevingh.com>
 */
class FileLocator extends Base
{
    /**
     * Adds a new directory path to the list of current paths.
     *
     * @param string $path A directory path.
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }
}

