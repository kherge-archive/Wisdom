<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom\Loader;

use RuntimeException;
use Symfony\Component\Yaml\Yaml as Base;

/**
 * Wisdom support for YAML files.
 *
 * @author Kevin Herrera <me@kevingh.com>
 */
class YAML extends Loader
{
    /** {@inheritDoc} */
    public function load($resource, $type = null)
    {
        if (false === ($data = file_get_contents($resource))) {
            throw new RuntimeException(sprintf(
                'Unable to read file: %s',
                $resource
            ));
        }

        return Base::parse($this->doReplace($data));
    }

    /** {@inheritDoc} */
    public function supports($resource, $type = null)
    {
        return ('yml' === pathinfo($resource, PATHINFO_EXTENSION));
    }
}

