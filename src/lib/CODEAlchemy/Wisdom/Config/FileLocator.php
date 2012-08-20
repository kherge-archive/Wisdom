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

use Symfony\Component\Config\FileLocator as Base;

/**
 * Allows paths to be added to FileLocator.
 *
 * @author Kevin Herrera <kherrera@codealchemy.com>
 */
class FileLocator extends Base
{
    /**
     * The directory path to add.
     *
     * @param string $path The directory path.
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }
}

