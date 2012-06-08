<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Loader;

    use RuntimeException,
        Symfony\Component\Config\Loader\FileLoader;

    /**
     * Wisdom support for INI files.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class INI extends FileLoader
    {
        /** {@inheritDoc} */
        public function load($resource, $type = null)
        {
            if (false === ($data = parse_ini_file($resource, true)))
            {
                throw new RuntimeException(
                    "Unable to parse file: $resource"
                );
            }

            return $data;
        }

        /** {@inheritDoc} */
        public function supports($resource, $type = null)
        {
            return ('ini' === pathinfo($resource, PATHINFO_EXTENSION));
        }
    }