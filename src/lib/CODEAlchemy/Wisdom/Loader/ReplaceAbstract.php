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

    use ArrayAccess,
        InvalidArgumentException,
        Symfony\Component\Config\Loader\FileLoader;

    /**
     * An extension of {@link FileLoader} with support for replacements.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    abstract class ReplaceAbstract extends FileLoader implements ReplaceInterface
    {
        /**
         * The replacement values.
         *
         * @type array|ArrayAccess
         */
        private $values;

        /** {@inheritDoc} */
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

        /** {@inheritDoc} */
        public function removeReplacementValues()
        {
            $this->values = null;
        }

        /** {@inheritDoc} */
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