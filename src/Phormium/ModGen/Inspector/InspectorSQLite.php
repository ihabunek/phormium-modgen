<?php

namespace Phormium\ModGen\Inspector;

use Phormium\DB;

class InspectorSQLite extends Inspector
{
    public function tableExists($database, $table)
    {
        $conn = DB::getConnection($database);
        $query = "
            SELECT count(*) AS count
            FROM sqlite_master
            WHERE name = :table
              AND type = 'table';
        ";
        $args = compact('table');
        $data = $conn->preparedQuery($query, $args);
        $count = $data[0]['count'];
        return $count > 0;
    }

    public function getTables($database)
    {
        $conn = DB::getConnection($database);
        $query = "
            SELECT tbl_name
            FROM sqlite_master
            WHERE type = 'table';
        ";
        $data = $conn->query($query);

        $tables = array();
        foreach($data as $row) {
            $tables[] = $row['tbl_name'];
        }

        return $tables;
    }

    public function getColumns($database, $table)
    {
        $conn = DB::getConnection($database);
        $query = "pragma table_info($table)";
        $data = $conn->query($query);

        $columns = array();
        foreach($data as $row) {
            $columns[] = $row['name'];
        }

        return $columns;
    }

    public function getPKColumns($database, $table)
    {
        $conn = DB::getConnection($database);
        $query = "pragma table_info($table)";
        $data = $conn->query($query);

        $columns = array();
        foreach($data as $row) {
            if ($row['pk'] == 1) {
                $columns[] = $row['name'];
            }
        }

        return $columns;
    }
}
