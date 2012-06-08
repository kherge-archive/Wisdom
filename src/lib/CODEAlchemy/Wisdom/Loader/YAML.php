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

    use Symfony\Component\Config\Loader\FileLoader,
        Symfony\Component\Yaml\Yaml as _Yaml;

    /**
     * Wisdom support for YAML files.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class YAML extends FileLoader
    {
        /** {@inheritDoc} */
        public function load($resource, $type = null)
        {
            return _Yaml::parse($resource);
        }

        /** {@inheritDoc} */
        public function supports($resource, $type = null)
        {
            return ('yml' == pathinfo($resource, PATHINFO_EXTENSION));
        }
    }