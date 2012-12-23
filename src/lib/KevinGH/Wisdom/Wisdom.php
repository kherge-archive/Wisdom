<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom;

use ArrayAccess;
use KevinGH\Wisdom\Config\FileLocator;
use KevinGH\Wisdom\Loader\Loader;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Loads configuration data using Symfony Config.
 *
 * @author Kevin Herrera <me@kevingh.com>
 */
class Wisdom
{
    /**
     * The cache directory path.
     *
     * @type string
     */
    private $cache = '';

    /**
     * The current debugging state.
     *
     * @type boolean
     */
    private $debug = false;

    /**
     * The customized FileLocator instance.
     *
     * @type FileLocator
     */
    private $locator;

    /**
     * The file name prefix.
     *
     * @type string
     */
    private $prefix = '';

    /**
     * The loader resolver.
     *
     * @type LoaderResolver
     */
    private $resolver;

    /**
     * The replacement values.
     *
     * @type array
     */
    private $values = array();

    /**
     * Sets the directory path(s) to locate files.
     *
     * @param array|string $paths The directory path(s).
     */
    public function __construct ($paths = array())
    {
        $this->locator = new FileLocator($paths);
        $this->resolver = new LoaderResolver;
    }

    /**
     * Adds the directory path.
     *
     * @param string $path The directory path.
     */
    public function addPath($path)
    {
        $this->locator->addPath($path);
    }

    /**
     * Adds the loader.
     *
     * @param Loader $loader The loader.
     */
    public function addLoader(Loader $loader)
    {
        $loader->setLocator($this->locator);

        $this->resolver->addLoader($loader);
    }

    /**
     * Returns the data for the configuration file.
     *
     * @param string       $file      The file name.
     * @param array|object $values    The new replacement values.
     * @param array        &$imported The list of imported resources.
     *
     * @return array The configuration data.
     *
     * @throws InvalidArgumentException If the value is not supported.
     * @throws LogicException           If a circular dependency is detected.
     */
    public function get($file, $values = null, array &$imported = array())
    {
        if ((null !== $values) && (false === is_array($values))) {
            if (false === ($values instanceof ArrayAccess)) {
                throw new InvalidArgumentException(
                    'The value of $values is not an array or an instance of ArrayAccess.'
                );
            }
        }

        if (null !== $values) {
            $values = $this->mergeValues($this->values, $values);
        } else {
            $values = $this->values;
        }

        $new = dirname($file) . DIRECTORY_SEPARATOR . $this->prefix . basename($file);

        try {
            $found = $this->locator->locate($new);
        } catch (InvalidArgumentException $first) {
            if (empty($this->prefix)) {
                throw $first;
            }

            try {
                $found = $this->locator->locate($file);
            } catch (InvalidArgumentException $second) {
                throw $first;
            }
        }

        if (in_array($found, $imported)) {
            throw new LogicException(sprintf(
                'Circular dependency detected for: %s',
                $found
            ));
        }

        $imported[] = $found;

        if (false === empty($this->cache)) {
            $cache = new ConfigCache(
                $this->cache . DIRECTORY_SEPARATOR . $file . '.php',
                $this->debug
            );

            if ($cache->isFresh()) {
                return $this->import(require $cache, $values, $imported);
            }
        }

        if (false === ($loader = $this->resolver->resolve($file))) {
            throw new InvalidArgumentException(sprintf(
                'No loader available for file: %s',
                $file
            ));
        }

        $loader->setValues($values);

        $data = $loader->load($found);

        if (isset($cache)) {
            $cache->write(
                '<?php return ' . var_export($data, true) . ';',
                array(new FileResource($found))
            );
        }

        return $this->import($data, $values, $imported);
    }

    /**
     * Manages the "imports" directive in configuration files.
     *
     * @param array        $data      The current data.
     * @param array|object $values    The new replacement values.
     * @param array        &$imported The list of imported resources.
     *
     * @return array The data merged with the imported resources.
     *
     * @throws LogicException If a circular reference is detected.
     */
    public function import(array $data, $values = null, array &$imported = array())
    {
        if (false === isset($data['imports'])) {
            return $data;
        }

        $imports = $data['imports'];

        unset($data['imports']);

        foreach ($imports as $resource) {
            $data = array_replace(
                $this->get($resource, $values, $imported),
                $data
            );
        }

        return $data;
    }

    /**
     * Sets the cache directory path.
     *
     * @param string $path The directory path.
     */
    public function setCache($path)
    {
        $this->cache = $path;
    }

    /**
     * Sets the debugging state.
     *
     * @param boolean $debug The debug state.
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Sets the file name prefix.
     *
     * @param string $prefix The file name prefix.
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets the default replacement values.
     *
     * @param array|object The replacement values.
     *
     * @throws InvalidArgumentException If the value is not supported.
     */
    public function setValues($values)
    {
        if ((null !== $values) && (false === is_array($values))) {
            if (false === ($values instanceof ArrayAccess)) {
                throw new InvalidArgumentException(
                    'The value of $values is not an array or an instance of ArrayAccess.'
                );
            }
        }

        $this->values = $values;
    }

    /**
     * Merges two sets of replacement values.
     *
     * @param array|object $a The replacement values.
     * @param array|object $b The replacement values.
     *
     * @return array The merged values.
     */
    private function mergeValues($a, $b)
    {
        $x = array();
        $y = array();

        if (is_array($a)) {
            $x = $a;
        }

        foreach ($b as $key => $value) {
            $x[$key] = $value;
        }

        return $x;
    }
}

