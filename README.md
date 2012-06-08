# Wisdom

Symfony Config for the rest of us.

[![Build Status](https://secure.travis-ci.org/codealchemy/Wisdom.png?branch=master)](http://travis-ci.org/codealchemy/Wisdom)

## What is it?

Wisdom is a configuration manager based on Symfony's Config component. A service provider is included for integration into a Silex-based web application.

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

You can use any class that implements `Symfony\Component\Config\Loader\LoaderInterface`.  By default, no loaders are setup, so you will need to add the ones you need.  Wisdom has a few loaders bundled to support the following data formats:

- INI
- JSON
- YAML (requires Symfony's YAML component)

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