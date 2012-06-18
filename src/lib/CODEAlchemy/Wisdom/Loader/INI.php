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

    use RuntimeException;

    /**
     * Wisdom support for INI files.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class INI extends Loader
    {
        /** {@inheritDoc} */
        public function load($resource, $type = null)
        {
            if (false === ($data = file_get_contents($resource)))
            {
                throw new RuntimeException(
                    "Unable to read file: $resource"
                );
            }

            $data = $this->doReplacements($data);

            if (false === ($data = parse_ini_string($data, true)))
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