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
        $query = "SELECT count(*) FROM systables WHERE tabname = :table";
        $data = $conn->preparedQuery($query, compact('table'), \PDO::FETCH_NUM);
        $count = $data[0][0];
        return $count > 0;
    }

    public function getTables($database)
    {
        $conn = DB::getConnection($database);
        $data = $conn->query("SELECT * FROM systables WHERE tabtype = 'T';", \PDO::FETCH_ASSOC);

        $tables = array();
        foreach($data as $row) {
            $table = $row['tabname'];

            // System tables have tabid < 100
            if ($row['tabid'] < 100) {
                continue;
            }

            // Ignore explicitely ignored tables
            if (in_array($table, $this->ignoreTables)) {
                continue;
            }

            $tables[] = $row['tabname'];
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
            WHERE st.tabname = :tabname;";
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
        SELECT
            c.constrid,
            c.idxname
        FROM
            sysconstraints c
        JOIN systables t ON t.tabid = c.tabid
        WHERE
            t.tabname = :tabname
            and c.constrtype = 'P' -- Primary key constraint
        ;";
        $args = array('tabname' => $table);

        $data = $conn->preparedQuery($query, $args);
        $count = count($data);

        if ($count == 0) {
            throw new \Exception("Cannot find primary key constraint for $database:$table.");
        }

        if ($count > 1) {
            throw new \Exception("Multiple primary key constraints found for $database:$table.");
        }

        $indexName = $data[0]['idxname'];

        // Fetch the constraing to find which columns are in it
        $query = "SELECT * FROM sysindexes WHERE idxname = :idxname;";
        $args = array('idxname' => $indexName);

        $data = $conn->preparedQuery($query, $args);
        $count = count($data);

        if ($count == 0) {
            throw new \Exception("Cannot find primary key index for $database:$table.");
        }

        if ($count > 1) {
            throw new \Exception("Multiple primary key indices found for $database:$table.");
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
