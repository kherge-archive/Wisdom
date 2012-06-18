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

    use Symfony\Component\Config\FileLocator,
        Symfony\Component\Config\Loader\FileLoader;

    /**
     * Covers basic required functionality for Wisdom loaders.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    abstract class Loader extends FileLoader
    {
        /**
         * The replacement values.
         *
         * @type array
         */
        private $values = array();

        /**
         * Removes the FileLoader's constructor arguments.
         */
        public function __construct()
        {
        }

        /**
         * Performs the replacement on the raw data.
         *
         * @param string $data The raw data.
         * @return string The replaced data.
         */
        public function doReplace($data)
        {
            $data = preg_replace_callback(
                '/#([^\s\r#]+)#/',
                function ($matches)
                {
                    if (defined($matches[1]))
                    {
                        return constant($matches[1]);
                    }

                    return $matches[0];
                },
                $data
            );

            if (isset($this->values))
            {
                $values = $this->values;

                $data = preg_replace_callback(
                    '/%([^\s\r%]+)%/',
                    function ($matches) use($values)
                    {
                        if (isset($values[$matches[1]]))
                        {
                            return $values[$matches[1]];
                        }

                        return $matches[0];
                    },
                    $data
                );
            }

            return $data;
        }

        /**
         * Sets or replaces the FileLocator instance.
         *
         * @param FileLocator $locator The FileLocator instance.
         */
        public function setLocator(FileLocator $locator)
        {
            $this->locator = $locator;
        }

        /**
         * Sets the replacement values.
         *
         * @param array|ArrayAccess The replacement values.
         */
        public function setValues($values)
        {
            $this->values = $values;
        }
    }