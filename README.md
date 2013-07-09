Phormium Model Generator
========================

Model class generator for [Phormium](https://github.com/ihabunek/phormium).

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
