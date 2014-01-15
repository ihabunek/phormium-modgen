Phormium Model Generator
========================

Model class generator for [Phormium](https://github.com/ihabunek/phormium).

Currently supports MySQL, PostgreSQL and Informix.

[![Build Status](https://travis-ci.org/ihabunek/phormium-modgen.png)](https://travis-ci.org/ihabunek/phormium)


Installation
------------

Install using [Composer](http://getcomposer.org/).

Create a file called `composer.json` with the following contents:
```json
{
    "require": {
        "phormium/modgen": "0.*"
    }
}
```

Download composer and run:
```
php composer.phar install
```

The script for running modgen will be `vendor/bin/modgen` for *nix and Mac and
`vendor\bin\modgen.bat` for Windows users. This is abbrevated to `modgen` in the
usage examples.

Usage
-----

Before starting, you need to have a Phormium configuration file which defines
the database from which you want to generate models. If a config file is not
specified, modgen will look for a file named "config.json" in the working
directory.

Generate models for all tables in a database:
```
modgen [options] <database>
```

Generate models for specified tables in a database:
```
modgen [options] <database> [table1] ... [tableN]
```

Options:

*  `--config`    - Path to the config file. (default: "config.json")
*  `--target`    - Target folder where the model will be generated. (default: "target")
*  `--namespace` - The PHP namespace used for the model classes. (default: "")
*  `--help (-h)` - Display the help message.

Examples
--------

Generate models for all tables in `backoffice` database, using namespace
`Foo\Bar`:

```
modgen --namespace=Foo\Bar backoffice
```

Generate models for tables `person` and `invoice` in the `backoffice` database,
without a namespace:

```
modgen backoffice person invoice
```

License
-------
Licensed under the MIT license. See [LICENSE.md](LICENSE.md).
