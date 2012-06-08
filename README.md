# Wisdom

Symfony Config for the rest of us.

[![Build Status](https://secure.travis-ci.org/codealchemy/Wisdom.png?branch=master)](http://travis-ci.org/codealchemy/Wisdom)

## What is it?

Wisdom is a configuration manager based on [Symfony's][Symfony] [Config][Config] component. A service provider is included for integration into a [Silex][Silex]-based web application.

## How do I install it?

Wisdom is designed to be installed with [Composer][Composer].  To add Wisdom to your composer package, you will want to merge this in with your `composer.json` file:

    {
        "require": {
            "codealchemy/wisdom": "1.*"
        }
    }

If you choose to not use Composer, the library also conforms to the [PSR-0][PSR0] standard.  This means you can easily load the Wisdom classes on demand using any PSR-0 compliant class loader.

## How do I use it?

    use CODEAlchemy\Wisdom\Loader\INI,
        CODEAlchemy\Wisdom\Wisdom;

    $wisdom = new Wisdom ('/path/to/config/');

    // Add support for INI files
    $wisdom->addLoader(new INI($wisdom->getLocator());

    $myData = $wisdom->get('myConfig.ini');

You may add directory paths when needed

    $wisdom->addPath('/another/path');

And you may also add new loaders when needed

    $wisdom->addLoader(new JSON($wisdom->getLocator()));

## What loaders can I use?

You can use any class that implements [`LoaderInterface`][LoaderInterface].  By default, no loaders are setup, so you will need to add the ones you need.  Wisdom has a few loaders bundled to support the following data formats:

- INI
- JSON
- YAML (requires Symfony's [YAML][YAML] component)

## How do I integrate it with Silex?

Integration with Silex is accomplished using the bundled service provider.  All that is required is that you specify what directory path(s) to use for locating configuration files.  You may also specificy some options, by the default values are shown below.

    use CODEAlchemy\Wisdom\Silex\Provider as WisdomProvider;

    $app->register(new WisdomProvider, array(
        'wisdom.path' => '/path/to/config', // or array of paths
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