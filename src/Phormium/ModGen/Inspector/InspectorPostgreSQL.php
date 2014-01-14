<?php

namespace Phormium\ModGen\Inspector;

use Phormium\DB;

class InspectorPostgreSQL extends Inspector
{
    public $ignoreTables = array();

    public function tableExists($database, $table)
    {
        $schema = $this->getSchema($database);
        $conn = DB::getConnection($database);
        $query = "
            SELECT count(*) as count
            FROM information_schema.tables
            WHERE table_schema = :schema
              AND table_name = :table
        ";
        $args = compact('schema', 'table');
        $data = $conn->preparedQuery($query, $args);
        $count = $data[0]['count'];
        return $count > 0;
    }

    public function getTables($database)
    {
        $schema = $this->getSchema($database);
        $conn = DB::getConnection($database);
        $query = "
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = :schema
        ";
        $args = compact('schema');
        $data = $conn->preparedQuery($query, $args);

        $tables = array();
        foreach($data as $row) {
            $tables[] = $row['table_name'];
        }

        return $tables;
    }

    public function getColumns($database, $table)
    {
        $schema = $this->getSchema($database);
        $conn = DB::getConnection($database);
        $query = "
            SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = :schema
              AND table_name = :table
              ORDER BY ordinal_position;
        ";
        $args = compact('schema', 'table');
        $data = $conn->preparedQuery($query, $args);

        $columns = array();
        foreach($data as $row) {
            $columns[] = $row['column_name'];
        }

        return $columns;
    }

    public function getPKColumns($database, $table)
    {
        $schema = $this->getSchema($database);
        $conn = DB::getConnection($database);

        // Find the primary key index name
        $query = "
            SELECT               
              pg_attribute.attname, 
              format_type(pg_attribute.atttypid, pg_attribute.atttypmod) 
            FROM pg_index, pg_class, pg_attribute 
            WHERE 
              pg_class.oid = :table::regclass AND
              indrelid = pg_class.oid AND
              pg_attribute.attrelid = pg_class.oid AND 
              pg_attribute.attnum = any(pg_index.indkey)
              AND indisprimary;
        ;";
        $args = compact('table');
        $data = $conn->preparedQuery($query, $args);

        $primaryKey = array();
        foreach($data as $row) {
            $primaryKey[] = $row['column_name'];
        }
        return $primaryKey;
    }

    private function getSchema($database)
    {
        $conn = DB::getConnection($database);
        $data = $conn->query('select current_schema() as db;');
        return $data[0]['db'];
    }
}
