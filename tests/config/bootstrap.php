<?php

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add('Phormium\\ModGen\\Tests', __DIR__ . '/../');

var_dump(getcwd(), PHORMIUM_CONFIG_FILE);
Phormium\DB::configure(PHORMIUM_CONFIG_FILE);
