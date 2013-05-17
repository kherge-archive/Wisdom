# Wisdom

> I am no longer maintaining this library. This project has been deprecated in favor of [Wise](https://github.com/herrera-io/php-wise). You can find the Silex service provider for Wise [here](https://github.com/herrera-io/php-silex-wise). If you would like to take over development of this project, please email me.

[![Build Status](https://secure.travis-ci.org/kherge/Wisdom.png?branch=master)](http://travis-ci.org/kherge/Wisdom)

Wisdom uses the Symfony Config component to load configuration settings from files.

## Installing

To install Wisdom, you must add it to the list of dependencies in your [`composer.json`][Composer] file.

    $ php composer.phar require kherge/wisdom=1.*

If you are not using Composer to manage your dependencies, you may use any [PSR-0][PSR-0] class loader to load Wisdom.

## Usage

Please see [the wiki][Wiki] for usage information.

[Composer]: http://getcomposer.org/
[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[Wiki]: https://github.com/kherge/Wisdom/wiki/Configure
