<?php

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add('Phormium\\ModGen\\Tests', __DIR__ . '/../');

Phormium\DB::configure(PHORMIUM_CONFIG_FILE);
