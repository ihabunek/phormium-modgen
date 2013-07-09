Phormium Model Generator
========================

Model class generator for [Phormium](https://github.com/ihabunek/phormium).

Currently supports MySQL and Informix. Support for PostgreSQL and SQLite is
pending.

Installation
------------

Install using [Composer](http://getcomposer.org/).

Create a file called `composer.json` with the following contents:
```json
{
    "require": {
        "phormium/modgen": "dev-master"
    },
    "minimum-stability": "dev"
}
```

Download composer and run:
```
php composer.phar install --no-dev
```

The script for running modgen will be: `vendor/bin/modgen`, abbrevated to
`modgen` in the usage examples.

Usage
-----

Generate models for all tables in a database:
```
modgen [options] <database>
```

Generate models for specified tables in a database:
```
modgen [options] <database> [table1] ... [tableN]
```

Options:

*  --config    - Path to the config file. (default: "config.json")
*  --target    - Target folder where the model will be generated. (default: "target")
*  --namespace - The PHP namespace used for the model classes. (default: "")
*  --help (-h) - Display the help message.

License
-------
Licensed under the MIT license. See [LICENSE.md](License.md).
