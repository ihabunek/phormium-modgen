<?php

$root = __DIR__ . '/../..';

$loader = require "$root/vendor/autoload.php";
$loader->add('Phormium\\ModGen\\Tests', "$root/tests/");

Phormium\DB::configure("$root/" . PHORMIUM_CONFIG_FILE);
