<?php

/* This file is part of Wisdom.
 *
 * (c) 2012 Kevin Herrera
 *
 * For the full copyright and license information, please
 * view the LICENSE file that was distributed with this
 * source code.
 */

namespace KevinGH\Wisdom\Silex;

use KevinGH\Wisdom\Wisdom;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * A Silex service provider for Wisdom.
 *
 * @author Kevin Herrera <me@kevingh.com>
 */
class Provider implements ServiceProviderInterface
{
    /** {@inheritDoc} */
    public function boot(Application $app)
    {
    }

    /**
     * Creates a Wisdom service provider.
     *
     * @param Application $app         The Silex application.
     * @param string      $serviceName The new service name.
     * @param string      $pathName    The new path parameter name.
     * @param string      $optionsName The new options parameter name.
     */
    public static function createService(Application $app, $serviceName, $pathName, $optionsName)
    {
        $app[$optionsName] = array();
        $app[$serviceName] = $app->share(
            function () use (
                $app,
                $serviceName,
                $pathName,
                $optionsName
                ) {
                $app[$optionsName] = array_merge(
                    array(
                        'cache_path' => '',
                        'debug' => $app['debug'],
                        'prefix' => $app['debug'] ? 'dev.' : 'prod.'
                    ),
                    $app[$optionsName]
                );

                if (false === isset($app[$optionsName]['loaders'])) {
                    $options = $app[$optionsName];

                    $options['loaders'] = array(
                        'KevinGH\Wisdom\Loader\INI',
                        'KevinGH\Wisdom\Loader\JSON'
                    );

                    if (class_exists('Symfony\Component\Yaml\Yaml')) {
                        $options['loaders'][] = 'KevinGH\Wisdom\Loader\YAML';
                    }

                    $app[$optionsName] = $options;
                }

                $wisdom = new Wisdom ($app[$pathName]);

                $wisdom->setCache($app[$optionsName]['cache_path']);
                $wisdom->setDebug($app[$optionsName]['debug']);
                $wisdom->setPrefix($app[$optionsName]['prefix']);
                $wisdom->setValues($app);

                foreach ((array) $app[$optionsName]['loaders'] as $class) {
                    $wisdom->addLoader(new $class);
                }

                return $wisdom;
            }
        );
    }

    /** {@inheritDoc} */
    public function register(Application $app)
    {
        self::createService($app, 'wisdom', 'wisdom.path', 'wisdom.options');
    }
}

