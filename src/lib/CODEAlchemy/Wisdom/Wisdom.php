<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom;

    use ArrayAccess,
        CODEAlchemy\Wisdom\Config\FileLocator,
        CODEAlchemy\Wisdom\Loader\ReplaceInterface,
        InvalidArgumentException,
        RuntimeException,
        Symfony\Component\Config\ConfigCache,
        Symfony\Component\Config\Loader\LoaderInterface,
        Symfony\Component\Config\Loader\LoaderResolver,
        Symfony\Component\Config\Resource\FileResource;

    /**
     * This class manages dependencies and configuration file loading.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class Wisdom
    {
        /**
         * The directory cache path.
         *
         * @type string
         */
        private $cachePath = '';

        /**
         * The debugging state.
         *
         * @type boolean
         */
        private $debug = false;

        /**
         * The FileLocator instance.
         *
         * @type FileLocator
         */
        private $locator;

        /**
         * The LoaderResolver instance.
         *
         * @type LoaderResolver
         */
        private $resolver;

        /**
         * The default replacement values.
         *
         * @type array|ArrayAccess
         */
        private $values;

        /**
         * Initializes the dependencies.
         *
         * @param array|string $paths The directory paths.
         * @param array|LoaderInterface $loaders The loaders.
         */
        public function __construct($paths = array(), array $loaders = array())
        {
            $this->locator = new FileLocator($paths);

            $this->resolver = new LoaderResolver($loaders);
        }

        /**
         * Adds the directory path to the file locator.
         *
         * @param string $path The directory path.
         */
        public function addPath($path)
        {
            $this->locator->addPath($path);
        }

        /**
         * Adds the loader to the loader resolver.
         *
         * @param LoaderInterface $loader The loader.
         */
        public function addLoader(LoaderInterface $loader)
        {
            if (false === ($loader instanceof ReplaceInterface))
            {
                throw new InvalidArgumentException(
                    "The loader does not implement ReplaceInterface."
                );
            }

            $this->resolver->addLoader($loader);
        }

        /**
         * Returns the data for the configuration file.  If $path is set to
         * true, a two element array is returned.  The first value is the data
         * parsed from the file.  The second value is the file path that the
         * data was parsed from.
         *
         * @param string $file The configuration file name.
         * @param boolean $path Return the file path too?
         * @param array $values The replacement values.
         * @return mixed The configuration file data.
         */
        public function get($file, $path = false, $values = null)
        {
            if (isset($values))
            {
                if (! (is_array($values) || ($values instanceof ArrayAccess)))
                {
                    throw new InvalidArgumentException(
                        'The $values argument is not an array or implements ArrayAccess.'
                    );
                }
            }

            else
            {
                $values = $this->values;
            }

            $found = $this->locator->locate($file);

            if ($this->isCache())
            {
                $cache = new ConfigCache(
                    $this->cachePath . DIRECTORY_SEPARATOR . $file . '.php',
                    $this->debug
                );

                if ($cache->isFresh())
                {
                    if ($path)
                    {
                        return array(include $cache, $found);
                    }

                    return include $cache;
                }
            }

            if (false === ($loader = $this->resolver->resolve($file)))
            {
                throw new RuntimeException(
                    "No available loader for file: $file"
                );
            }

            $loader->setReplacementValues($values);

            $data = $loader->load($found);

            $loader->removeReplacementValues();

            if ($this->isCache())
            {
                $cache->write(
                    '<?php return ' . var_export($data, true) . ';',
                    array(new FileResource($found))
                );
            }

            if ($path)
            {
                return array($data, $found);
            }

            return $data;
        }

        /**
         * Returns the cache directory path.
         *
         * @return string The cache directory path.
         */
        public function getCachePath()
        {
            return $this->cachePath;
        }

        /**
         * Returns the loaders in the loader resolver.
         *
         * @return LoaderInterface[] The loaders.
         */
        public function getLoaders()
        {
            return $this->resolver->getLoaders();
        }

        /**
         * Returns the {@link FileLocator} instance.
         *
         * @return FileLocator The FileLocator instance.
         */
        public function getLocator()
        {
            return $this->locator;
        }

        /**
         * Returns the directory paths in the file locator.
         *
         * @return array The directory paths.
         */
        public function getPaths()
        {
            return $this->locator->getPaths();
        }

        /**
         * Returns the default replacement values.
         *
         * @return array|ArrayAccess The replacement values.
         */
        public function getReplacementValues()
        {
            return $this->values;
        }

        /**
         * Returns the current caching state.
         *
         * @return boolean TRUE if enabled, FALSE if not.
         */
        public function isCache()
        {
            return ! empty($this->cachePath);
        }

        /**
         * Returns the current debugging state.
         *
         * @return boolean TRUE if enabled, FALSE if not.
         */
        public function isDebug()
        {
            return $this->debug;
        }

        /**
         * Sets the cache directory path.
         *
         * @throws InvalidArgumentException If the path is to a file.
         * @throws RuntimeException If the directory could not be created.
         * @param string $cachePath The cache directory path.
         */
        public function setCachePath($cachePath)
        {
            if (empty($cachePath))
            {
                $this->cachePath = '';

                return;
            }

            if (file_exists($cachePath))
            {
                if (! is_dir($cachePath))
                {
                    throw new InvalidArgumentException(
                        "The cache path is not a directory: $cachePath"
                    );
                }
            }

            elseif (false === mkdir($cachePath, 0755, true))
            {
                throw new RuntimeException(
                    "Unable to create cache directory: $cachePath"
                );
            }

            $this->cachePath = $cachePath;
        }

        /**
         * Sets the new debugging state.
         *
         * @param boolean $debug The new debugging state.
         */
        public function setDebug($debug)
        {
            $this->debug = $debug;
        }

        /**
         * Sets the default replacement values.
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