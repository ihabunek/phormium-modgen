<?php

namespace Phormium\ModGen;

use Phormium\DB;

class Generator
{
    /** Maps drivers to corresponding inspectors. */
    private static $driverMap = array(
        'informix' => '\\Phormium\\ModGen\\Inspector\\InspectorInformix',
        'mysql' => '\\Phormium\\ModGen\\Inspector\\InspectorMySQL',
        'pgsql' => '\\Phormium\\ModGen\\Inspector\\InspectorPostgreSQL'
    );

    /** Full path to the target directory. */
    protected $target;

    /** Namespace for model classes. */
    protected $namespace;

    public function __construct($target)
    {
        if (!is_dir($target)) {
            throw new \RuntimeException("Given target dir does not exist: $target");
        }
        $this->target = realpath($target);
    }

    /**
     * Determines the target directory for a Model subclass based on namespace.
     * If the directory does not exist, creates it.
     */
    private function getTargetDirectory($namespace)
    {
        $target = $this->target;
        if (!empty($namespace)) {
            $subDir = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            $subDir = trim($subDir, DIRECTORY_SEPARATOR);
            $target .= DIRECTORY_SEPARATOR . $subDir;
        }

        if (!is_dir($target)) {
            if (!mkdir($target, 0777, true)) {
                throw new \Exception("Failed creating target directory [$target].");
            }
        }

        return $target;
    }

    public function getInspector($database)
    {
        $driver = DB::getConnection($database)->getDriver();
        if (!isset(self::$driverMap[$driver])) {
            throw new \Exception("Driver [$driver] is currently not supported.");
        }
        $class = self::$driverMap[$driver];
        return new $class();
    }

    /**
     * Generates the Model class code for a given table and saves it to the
     * target directory.
     * @return array Array with two string values: name of the generated class
     *      and the path to which the class was saved.
     */
    public function generateModel($database, $table, $namespace = null)
    {
        $model = $this->generateModelCode($database, $table, $namespace);
        $class = $this->getModelName($table);

        $target = $this->getTargetDirectory($namespace);
        $path = "$target" . DIRECTORY_SEPARATOR . "$class.php";

        $success = file_put_contents($path, $model);
        if ($success === false) {
            throw new \Exception("Failed saving model to [$path].");
        }

        return array($class, $path);
    }

    /**
     * Generates the Model class code for a given table.
     * @return string
     */
    public function generateModelCode($database, $table, $namespace = null)
    {
        $inspector = $this->getInspector($database);
        if (!$inspector->tableExists($database, $table)) {
            throw new \Exception("Table [$table] does not exist.");
        }

        $columns = $inspector->getColumns($database, $table);
        $pkColumns = $inspector->getPKColumns($database, $table);
        $class = $this->getModelName($table);

        $meta = array(
            'database' => $database,
            'table' => $table
        );

        // Add primary key if any
        $pkCount = count($pkColumns);
        if ($pkCount > 1) {
            $meta['pk'] = $pkColumns;
        } elseif ($pkCount == 1) {
            $meta['pk'] = $pkColumns[0];
        }

        // Convert to PHP code and improve formatting
        $meta = var_export($meta, true);
        $meta = str_replace("=> \n  array", "=> array", $meta);
        $meta = str_replace('  ', '    ', $meta);
        $meta = str_replace("\n", "\n    ", $meta);
        $meta = preg_replace("/[0-9]+ => /", "", $meta);

        $model  = "<?php\n\n";

        if (!empty($namespace)) {
            $model .= "namespace $namespace;\n\n";
        }

        $model .= "class $class extends \\Phormium\\Model\n";
        $model .= "{\n";
        $model .= "    protected static \$_meta = $meta;\n\n";

        foreach ($columns as $column)
        {
            $model .= "    public \$".strtolower($column).";\n";
        }
        $model .= "}\n";

        return $model;
    }

    /** Generates a Model class name in CamelCase based on the table name. */
    protected function getModelName($table)
    {
        $class = str_replace('_', ' ', $table);
        $class = ucwords($class);
        $class = str_replace(' ', '', $class);

        return $class;
    }
}
