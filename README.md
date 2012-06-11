# Wisdom

Symfony Config for the rest of us.

[![Build Status](https://secure.travis-ci.org/codealchemy/Wisdom.png?branch=master)](http://travis-ci.org/codealchemy/Wisdom)

## What is it?

Wisdom is a library for retrieving configuration data from files.  The library relies on the [Symfony][Symfony] [Config][Config] component to find the files and cache the parsed results.  A service provider for [Silex][Silex] is also included.

## How do I install it?

The library is a [Composer][Composer] package.  To install it, you will want to add the following information to your `composer.json` file:

    {
        "require": {
            "codealchemy/wisdom": "1.0.*"
        }
    }

If you are not using Composer, you may simply clone it using Git.  The library follows the [PSR-0][PSR0] standard, making it compatible with any PSR-0 compliant class loader.

## How do I use it?

The quickest way to setup Wisdom is this:

    use CODEAlchemy\Wisdom\Loader\INI,
        CODEAlchemy\Wisdom\Wisdom;

    $wisdom = new Wisdom('/path/to/config');

    $wisdom->addLoader(new INI($wisdom->getLoader()));

    $settings = $wisdom->get('mySettings.ini');

This example will load any INI configuration files from the `/path/to/config` directory.  To enable caching, support for additional loaders, and more, I advise that you read the API documentation.

### Enabling caching

    $wisdom->setCachePath('/path/to/cache/dir');

Conditions for when the cache is refreshed (set by the `Symfony\Component\Config\ConfigCache` class):

- The cache file does not exist
- Debugging is enabled
    - The original file has been updated

### Supporting other formats

Wisdom has loaders for INI, JSON, and YAML files:

    CODEAlchemy\Wisdom\Loader\INI
    CODEAlchemy\Wisdom\Loader\JSON
    CODEAlchemy\Wisdom\Loader\YAML

> Note that in order to use the included YAML loader, you will need to have the Symfony [YAML][YAML] component installed.

To register one of these loaders, you call the `addLoader()` method:

    $wisdom->addLoader(new JSON($wisdom->getLocator());

The `getLocator()` method is the `FileLocator` instance used by Wisdom to manage configuration directory paths.  It is advised that you use the one provided by Wisdom instead of creating your own.  To support other data formats, you can create your own loader by implementing `Symfony\Component\Config\Loader\LoaderInterface` and register it the same way.

## How do I integrate it with Silex?

Integration with Silex can be done using the bundled service provider.  All that is required is that you specify what directory path(s) to use for locating files.  There are optional settings you can specify, of which their defaults are shown below.

    use CODEAlchemy\Wisdom\Silex\Provider as WisdomProvider;

    $app->register(new WisdomProvider, array(

        // required
        'wisdom.path' => '/path/to/config', // or array of paths

        // all optional
        'wisdom.options' => array(
            'cache_path' => '',
            'debug' => $app['debug'],
            'loader' => array(
                'CODEAlchemy\Wisdom\Loader\INI',
                'CODEAlchemy\Wisdom\Loader\JSON',
                'CODEAlchemy\Wisdom\Loader\YAML'
            )
        )
    ));

[Symfony]: http://symfony.com/
[Config]: http://github.com/symfony/Config
[Composer]: http://getcomposer.org/
[PSR0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[Silex]: http://silex.sensiolabs.org/
[LoaderInterface]: https://github.com/symfony/Config/blob/master/Loader/LoaderInterface.php
[YAML]: http://symfony.com/doc/current/components/yaml.html