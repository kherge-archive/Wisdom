<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Silex;

    use CODEAlchemy\Wisdom\Wisdom,
        Silex\Application,
        Silex\ServiceProviderInterface;

    /**
     * A Silex service provider for Wisdom.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
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
         * @param string $serviceName ?
         * @param string $pathName ?
         * @param string $optionsName ?
         */
        public static function createService(Application $app, $serviceName, $pathsName, $optionsName)
        {
            $app[$optionsName] = array();

            $app[$serviceName] = $app->share(function () use ($app, $serviceName, $pathsName, $optionsName)
            {
                $app[$optionsName] = array_merge(
                    array(
                        'cache_path' => '',
                        'debug' => $app['debug'],
                        'prefix' => $app['debug'] ? 'dev.' : 'prod.'
                    ),
                    $app[$optionsName]
                );

                if (false === isset($app[$optionsName]['loaders']))
                {
                    $options = $app[$optionsName];

                    $options['loaders'] = array(
                        'CODEAlchemy\Wisdom\Loader\INI',
                        'CODEAlchemy\Wisdom\Loader\JSON'
                    );

                    if (class_exists('Symfony\Component\Yaml\Yaml'))
                    {
                        $options['loaders'][] = 'CODEAlchemy\Wisdom\Loader\YAML';
                    }

                    $app[$optionsName] = $options;
                }

                $wisdom = new Wisdom ($app[$pathsName]);

                $wisdom->setCache($app[$optionsName]['cache_path']);
                $wisdom->setDebug($app[$optionsName]['debug']);
                $wisdom->setPrefix($app[$optionsName]['prefix']);
                $wisdom->setValues($app);

                foreach ((array) $app[$optionsName]['loaders'] as $class)
                {
                    $wisdom->addLoader(new $class);
                }

                return $wisdom;
            });
        }

        /** {@inheritDoc} */
        public function register(Application $app)
        {
            self::createService($app, 'wisdom', 'wisdom.path', 'wisdom.options');
        }
    }