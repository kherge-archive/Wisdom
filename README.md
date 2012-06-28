# Wisdom

[![Build Status](https://secure.travis-ci.org/codealchemy/Wisdom.png?branch=master)](http://travis-ci.org/codealchemy/Wisdom)

Wisdom uses the Symfony Config component to load configuration settings from files.

    <?php

        use CODEAlchemy\Wisdom\Loader\YAML,
            CODEAlchemy\Wisdom\Wisdom;

        $wisdom = new Wisdom('/path/to/config');

        $wisdom->addLoader(new YAML);

        $config = $wisdom->get('config.yml');

Wisdom also supports Silex.

    <?php
    
        use CODEAlchemy\Wisdom\Silex\Provider;

        $app->register(new Provider, array(
            'wisdom.path' => '/path/to/config'
        ));

        $config = $app['wisdom']->get('config.yml');

## Installing

To install Wisdom, you must add it to the list of dependencies in your [`composer.json`][Composer] file.

    {
        "require": {
            "codealchemy/wisdom": "1.0.*"
        }
    }

Once that is done, update your installed dependencies.

    php composer.phar update

If you are not using [Composer][Composer] to manage your dependencies, you may use any [PSR-0][PSR-0] class loader to load Wisdom.

## Configuring

Please see the [wiki for detailed instructions][Wiki] on how to configure Wisdom.

[Composer]: http://getcomposer.org/
[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[Wiki]: https://github.com/codealchemy/Wisdom/wiki/Configure