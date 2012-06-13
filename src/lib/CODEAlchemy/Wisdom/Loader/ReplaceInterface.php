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

    use ArrayAccess;

    /**
     * The interface used to support placeholder replacements in raw data.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    interface ReplaceInterface
    {
        /**
         * Performs the placeholder replacements.
         *
         * @param string $data The raw data to modify.
         * @return string The modified raw data.
         */
        public function doReplacements($data);

        /**
         * Removes the replacement values.
         */
        public function removeReplacementValues();

        /**
         * Sets the replacement values.
         *
         * @param array|ArrayAccess $values The replacement values.
         */
        public function setReplacementValues($values);
    }