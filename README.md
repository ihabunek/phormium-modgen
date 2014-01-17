Phormium Model Generator
========================

Model class generator for [Phormium](https://github.com/ihabunek/phormium).

ModGen generates classes which extend Phormium\Model from existing database
tables. Currently supports MySQL, PostgreSQL, SQLite and Informix.

[![Latest Stable Version](https://poser.pugx.org/phormium/modgen/v/stable.png)](https://packagist.org/packages/phormium/modgen)
[![Total Downloads](https://poser.pugx.org/phormium/modgen/downloads.png)](https://packagist.org/packages/phormium/modgen)
[![Build Status](https://travis-ci.org/ihabunek/phormium-modgen.png)](https://travis-ci.org/ihabunek/phormium-modgen)

Installation
------------

### PHAR

The simplest way to use ModGen is to download the latest version as a
PHAR archive from the
[releases page](https://github.com/ihabunek/phormium-modgen/releases) on GitHub.

You can now run ModGen:

```
php modgen.phar
```

This is abbreviated to `modgen` in the [usage examples](#usage).

### Packagist

You can also install from [Packagist](https://packagist.org/) using using
[Composer](http://getcomposer.org/).

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
`vendor\bin\modgen.bat` for Windows users. This is abbreviated to `modgen` in
the [usage examples](#usage).

Creating the PHAR
-----------------

To compile ModGen into a PHAR, clone the project from GitHub:
```
git clone https://github.com/ihabunek/phormium-modgen.git
```

Install the dependencies using Composer:
```
php composer.phar install
```

Run the compilation script:
```
php bin\compile
```

`modgen.phar` will be generated in your working directory.


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
