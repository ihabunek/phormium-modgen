<?php

namespace Phormium\ModGen\Inspector;

use Phormium\DB;

class InspectorInformix extends Inspector
{
    // System tables which have tabid > 100
    public $ignoreTables = array(
        'sysblderrorlog',
        'sysbldobjects',
        'sysbldobjdepends',
        'sysbldobjkinds',
        'sysbldregistered',
        'sysbldiprovided',
        'sysbldirequired',
        'bld_registered',
        'bldi_provided',
        'bldi_required',
    );

    public function tableExists($database, $table)
    {
        $conn = DB::getConnection($database);
        $query = "
            SELECT count(*) AS count
            FROM systables
            WHERE tabname = :table
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
            SELECT tabid, tabname
            FROM systables
            WHERE tabtype = 'T'
              AND tabid >= 100 -- skip system tables
            ORDER BY tabname;
        ";

        $data = $conn->query($query);

        $tables = array();
        foreach($data as $row) {
            // Ignore explicitely ignored tables
            $table = $row['tabname'];
            if (!in_array($table, $this->ignoreTables)) {
                $tables[] = $table;
            }
        }

        return $tables;
    }

    public function getColumns($database, $table)
    {
        $conn = DB::getConnection($database);
        $query = "
            SELECT colno, colname
            FROM syscolumns sc
            JOIN systables st ON st.tabid = sc.tabid
            WHERE st.tabname = :tabname;
        ";

        $args = array('tabname' => $table);
        $data = $conn->preparedQuery($query, $args);

        $columns = array();
        foreach($data as $row) {
            $columns[$row['colno']] = $row['colname'];
        }

        return $columns;
    }

    public function getPKColumns($database, $table)
    {
        $conn = DB::getConnection($database);

        // Find the primary key index name
        $query = "
            SELECT c.idxname
            FROM sysconstraints c
            JOIN systables t ON t.tabid = c.tabid
            WHERE t.tabname = :tabname
              AND c.constrtype = 'P' -- Primary key constraint
        ;";

        $args = array('tabname' => $table);
        $data = $conn->preparedQuery($query, $args);

        if (empty($data)) {
            return array();
        }

        $indexName = $data[0]['idxname'];

        // Fetch the constraing to find which columns are in it
        $query = "
            SELECT *
            FROM sysindexes
            WHERE idxname = :idxname;
        ";

        $args = array('idxname' => $indexName);
        $data = $conn->preparedQuery($query, $args);

        if (empty($data)) {
            return array();
        }

        $index = $data[0];
        $columns = $this->getColumns($database, $table);
        $primaryKey = array();

        foreach(range(1, 16) as $no) {
            $col = "part$no";
            if ($index[$col] > 0) {
                $primaryKey[] = $columns[$index[$col]];
            }
        }

        return $primaryKey;
    }
}
