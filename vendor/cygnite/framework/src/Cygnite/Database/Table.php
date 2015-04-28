<?php
/*
 * This file is part of the Cygnite package.
 *
 * (c) Sanjoy Dey <dey.sanjoy0@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cygnite\Database;

use Cygnite\Database\Connection;

class Table extends Connection
{

    private $_connection;

    private $schemaInstance;

    public $database;

    public $tableName;

    protected $primaryKey;

    private $query;

    private $statement;

    public function connect($database, $model)
    {
        $this->database = $database;
        $this->tableName = $model;
        $this->_connection = Connection::getConnection($database);

        return $this;
    }

    public function getDefaultDatabaseConnection()
    {
        return Connection::getDefaultConnection();
    }

    public function getColumns()
    {
        $conn = null;
        $me = $this;
        $conn = $this->_connection;
        $schema = Schema::instance(
            $this,
            function($table) use ($me) {
                $table->tableName = $me->tableName;
                $columns = null;
                //$table->setDbConnection($this->_connection, $this->database);
                $table->setTableSchema();

                return $table->schema;
            }
        );

        //$columns = $conn->query($table->schema)->fetchAll();
        $columns = $this->query($schema)->getAll();
        $this->setSchemaInstance($columns);

        return $this->getSchemaInstance();
    }

    /**
     * Set Schema Instance
     * @param $instance
     */
    public function setSchemaInstance($instance)
    {
        $this->schemaInstance = $instance;
    }

    /**
     * Get schema instance
     *
     * @return null
     */
    public function getSchemaInstance()
    {
        return !is_null($this->schemaInstance) ? $this->schemaInstance : null;
    }


    /**
     * @param null $queryString
     * @return $this
     */
    private function query($queryString = null)
    {
        $query = ($queryString == null) ? $this->query : $queryString;

        $this->statement = $this->_connection->prepare($query);
        $this->statement->execute();

        return $this;
    }

    public function getAll()
    {
        return $this->statement->fetchAll();
    }

    /**
     * @param string $tableName
     */
    public function makeMigration($tableName = 'migrations')
    {
        $this->connect(
            trim($this->getDefaultDatabaseConnection()),
            $tableName
        );

        $me = $this;

        //Create migration table in order to save migrations information
        Schema::instance($this,
            function($table) use ($tableName, $me){
                $table->tableName = $tableName;
                $table->database = trim($me->getDefaultDatabaseConnection());
                $table->create(
                    array(
                        array('column'=> 'id', 'type' => 'int', 'length' => 11,
                            'increment' => true, 'key' => 'primary'),
                        array('column'=> 'migration', 'type' => 'string', 'length' =>255),
                        array('column'=> 'version', 'type' => 'int'),
                        array('column'=> 'created_at',  'type' => 'datetime')
                    ),
                    'InnoDB',
                    'latin1'
                )->run();
            }
        );
    }

    /**
     * @param $migration
     * @return mixed
     */
    public function updateMigrationVersion($migration)
    {
        $date = new \DateTime("now");

        $date->setTimezone(new \DateTimeZone(SET_TIME_ZONE));

        $migrationName = $migration->getVersion().$migration->getMigrationClass();

        $this->connect(
            trim($this->getDefaultDatabaseConnection()),
            'migrations'
        );

        $sql = "INSERT INTO migrations (`migration`,  `created_at`)
                VALUES('".$migrationName."',
                          '".$date->format('Y-m-d H:i:s')."'
                      )";

        return $this->_connection->prepare($sql)->execute();

    }
}
