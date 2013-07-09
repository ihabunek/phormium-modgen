<?php

namespace Phormium\ModGen\Inspector;

abstract class Inspector
{
    /** Check wether a table exists in the given database. */
    abstract public function tableExists($database, $table);

    /** Returns an array of table names found at given database. */
    abstract public function getTables($database);

    /** Returns an array of columns found in given table. */
    abstract public function getColumns($database, $table);

    /** Returns an array of primary key columns for given table. */
    abstract public function getPKColumns($database, $table);
}
