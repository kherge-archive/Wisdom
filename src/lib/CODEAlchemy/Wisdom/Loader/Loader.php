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

    use InvalidArgumentException,
        Symfony\Component\Config\FileLocatorInterface,
        Symfony\Component\Config\Loader\FileLoader;

    /**
     * Manages Wisdom-specific loader functionality.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    abstract class Loader extends FileLoader
    {
        /**
         * The replacement values.
         *
         * @type array|ArrayAccess
         */
        private $values;

        /**
         * Removes constructor requirements set by {@link FileLoader}.
         */
        public function __construct()
        {
        }

        /**
         * Performs the placeholder replacements.
         *
         * @param string $data The raw data to modify.
         * @return string The modified raw data.
         */
        public function doReplacements($data)
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
         * Removes the replacement values.
         */
        public function removeReplacementValues()
        {
            $this->values = null;
        }

        /**
         * Sets the FileLocator instance.
         *
         * @param FileLocatorInterface $locator The FileLocator instance.
         */
        public function setFileLocator(FileLocatorInterface $locator)
        {
            $this->locator = $locator;
        }

        /**
         * Sets the replacement values.
         *
         * @param array|ArrayAccess $values The replacement values.
         */
        public function setReplacementValues($values)
        {
            if (null !== $values)
            {
                if (! (is_array($values) || ($values instanceof ArrayAccess)))
                {
                    throw new InvalidArgumentException(
                        'The $values argument is not an array or implements ArrayAccess.'
                    );
                }
            }

            $this->values = $values;
        }
    }