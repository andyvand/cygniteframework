<?php
namespace Cygnite\Console\Command;

use Cygnite\Foundation\Application;
use Cygnite\Helpers\Inflector;
use Cygnite\Database;
use Cygnite\Database\Schema;
use Cygnite\Console\Generator\Model;
use Cygnite\Console\Generator\View;
use Cygnite\Console\Generator\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 *  Cygnite Framework
 *
 *  An open source application development framework for PHP 5.3 or newer
 *
 *   License
 *
 *   This source file is subject to the MIT license that is bundled
 *   with this package in the file LICENSE.txt.
 *   http://www.cygniteframework.com/license.txt
 *   If you did not receive a copy of the license and are unable to
 *   obtain it through the world-wide-web, please send an email
 *   to sanjoy@hotmail.com so that I can send you a copy immediately.
 *
 * @Package               :  Console
 * @Filename              :  GeneratorCommand.php
 * @Description           :  Generator Command class used to generate crud application using Cygnite CLI.
 *                           Cygnite CLI driven by Symfony2 Console Component.
 * @Author                :  Sanjoy Dey
 * @Copyright             :  Copyright (c) 2013 - 2014,
 * @Link                  :  http://www.cygniteframework.com
 * @Since                 :  Version 1.0.6
 * @File                     Source
 *
 */

class GeneratorCommand extends Command
{

    public $applicationDir;
    public $controller;
    public $model;
    public $database;
    private $tableSchema;
    private $inflect;
    private $columns;
    private $output;
    private $viewType;

    public static function __callStatic($method, $arguments = array())
    {
        if ($method == 'instance') {
            return new self();
        }
    }

    public function setSchema($table)
    {
        $this->tableSchema = $table;
    }

    /**
     * Get primary key of the table
     *
     * @return null
     */
    public function getPrimaryKey()
    {
        $primaryKey = null;

        if (count($this->columns) > 0) {
            foreach ($this->columns as $key => $value) {
                if ($value->column_key == 'PRI' || $value->extra == 'auto_increment') {
                    $primaryKey = $value->column_name;
                    break;
                }
            }
        }

        return $primaryKey;
    }

    protected function configure()
    {
        $this->setName('generate:crud')
            ->setDescription('Generate Sample Crud Application Using Cygnite CLI')
            ->addArgument('name', InputArgument::OPTIONAL, 'Your Controller Name ?')
            ->addArgument('model', InputArgument::OPTIONAL, 'Your Model Name ?')
            ->addArgument('database', InputArgument::OPTIONAL, '')
            ->addOption('template', null, InputOption::VALUE_NONE, 'If set, will use twig template for view page.');
    }

    /**
     * We will execute the crud command and generate files
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Your controller name
        $this->controller = Inflector::classify($input->getArgument('name')) . 'Controller';
        // Model name
        $this->model = Inflector::classify($input->getArgument('model'));
        /** Check for argument database name if not given we will use default
         *  database connection
         */
        $this->database = (!is_null($input->getArgument('database'))) ?
            $input->getArgument('database') :
            $this->tableSchema->getDefaultDatabaseConnection();

        // By default we will generate plain php layout and view pages
        $this->viewType = ($input->getOption('template') == false) ? 'php' : 'twig';

        $this->columns = $this->getColumns();

        if (empty($this->columns)) {
            throw new \Exception("Please provide valid table name. It seems doesn't exists in the database.");
        }

        $this->applicationDir = BASE_PATH . DS . APP_PATH;
        $this->output = $output;

        $this->generateController();
        $this->generateModel();
        $this->generateViews();

        $output->writeln("<info>Crud Generated Successfully By Cygnite Cli.</info>");
    }

    /**
     * We will get all column schema from database
     *
     * @return mixed
     */
    private function getColumns()
    {
        return $this->tableSchema->connect(
            $this->database,
            Inflector::tabilize($this->model)
        )->getColumns();
    }

    /**
     * We will generate Controller
     */
    private function generateController()
    {
        // Generate Controller class
        $controllerInstance = Controller::instance($this->columns, $this->viewType, $this);

        $controllerTemplateDir =
            dirname(dirname(__FILE__)) . DS . 'src' . DS . ucfirst('apps') . DS . ucfirst('controllers') . DS;

        $controllerInstance->setControllerTemplatePath($controllerTemplateDir);
        $controllerInstance->setApplicationDirectory($this->applicationDir);

        $controllerInstance->setControllerName($this->controller);
        $controllerInstance->setModelName($this->model);
        $controllerInstance->updateTemplate();
        $controllerInstance->generateControllerTemplate();

        $controllerInstance->generate();

        $this->output->writeln("Controller $this->controller generated successfully..");
    }

    /**
     * We will generate model here
     */
    private function generateModel()
    {
        $modelInstance = Model::instance($this);
        $modelTemplateDir =
            dirname(dirname(__FILE__)) . DS . 'src' . DS . ucfirst('apps') . DS . ucfirst('models') . DS;

        $modelInstance->setModelTemplatePath($modelTemplateDir);
        $modelInstance->updateTemplate();
        $modelInstance->generate();
        $this->output->writeln("Model $this->model generated successfully..");
    }

    /**
     * We will generate the view pages into views directory
     */
    private function generateViews()
    {
        $viewInstance = View::instance($this);
        $viewInstance->setLayoutType($this->viewType);
        $viewTemplateDir = dirname(dirname(__FILE__)) . DS . 'src' . DS . ucfirst('apps') . DS . ucfirst('views') . DS;
        $viewInstance->setTableColumns($this->columns);
        $viewInstance->setViewTemplatePath($viewTemplateDir);

        // generate twig template layout if type has set via user
        if ($this->viewType == 'php') {
            // Type not set then we will generate php layout
            $viewInstance->generateLayout('layout');
        } else {
            $viewInstance->generateLayout('layout.main');
        }

        $viewInstance->generateViews();

        $this->output->writeln(
            "Views generated in " . str_replace("Controller", "", $this->controller) . " directory.."
        );
    }
}
