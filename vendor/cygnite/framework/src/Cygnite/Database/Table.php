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

    private $prepareQuery;

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
        Schema::instance(
            $this,
            function($table) use ($me) {
                $table->tableName = $me->tableName;
                $columns = null;
                //$table->setDbConnection($this->_connection, $this->database);
                $table->setTableSchema();
                //$columns = $conn->query($table->schema)->fetchAll();
                $columns = $me->query($table->schema)->getAll();

                $me->schemaInstance = $columns;
            }
        );

        return $this->schemaInstance;
    }

    private function query($queryString = null)
    {
        $query = ($queryString == null) ? $this->query : $queryString;

        $this->prepareQuery = $this->_connection->prepare($query);
        $this->prepareQuery->execute();

        return $this;
    }

    public function getAll()
    {
        return $this->prepareQuery->fetchAll();
    }

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
                        array('name'=> 'id', 'type' => 'int', 'length' => 11,
                            'increment' => true, 'key' => 'primary'),
                        array('name'=> 'migration', 'type' => 'string', 'length' =>255),
                        array('name'=> 'version', 'type' => 'int', 'length' =>11),
                        array('name'=> 'created_at',  'type' => 'datetime',
                        'length'  =>"DEFAULT '0000-00-00 00:00:00'"
                        ),
                    ),
                    'InnoDB',
                    'latin1'
                )->run();
            }
        );
    }

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
