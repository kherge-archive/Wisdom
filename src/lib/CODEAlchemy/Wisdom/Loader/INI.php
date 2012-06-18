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
     * Offers support for loading INI files.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class INI extends Loader
    {
        /** {@inheritDoc} */
        public function load ($resource, $type = null)
        {
            if (false === ($data = file_get_contents($resource)))
            {
                throw new RuntimeException(sprintf('Unable to read file: %s', $resource));
            }

            $data = $this->doReplace($data);

            if (false === ($data = parse_ini_string($data, true)))
            {
                throw new RuntimeException(sprintf('Unable to parse file: %s', $resource));
            }

            return $data;
        }

        /** {@inheritDoc} */
        public function supports($resource, $type = null)
        {
            return ('ini' === pathinfo($resource, PATHINFO_EXTENSION));
        }
    }