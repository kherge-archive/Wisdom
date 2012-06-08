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

    use Symfony\Component\Config\FileLocator as _FileLocator;

    /**
     * A simple extension of {@link _FileLocator} that can modify paths.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class FileLocator extends _FileLocator
    {
        /**
         * Adds the directory path.
         *
         * @param string $path The directory path.
         */
        public function addPath($path)
        {
            $this->paths[] = $path;
        }

        /**
         * Returns the directory paths.
         *
         * @return array The directory paths.
         */
        public function getPaths()
        {
            return $this->paths;
        }
    }