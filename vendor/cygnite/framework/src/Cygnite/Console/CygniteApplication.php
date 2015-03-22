<?php
namespace Cygnite\Console;

use Cygnite\Database;
use Cygnite\Database\Table;
use Cygnite\Console\Command\InitCommand;
use Cygnite\Console\Command\GeneratorCommand;
use Cygnite\Console\Command\MigrationCommand;
use Symfony\Component\Console\Application;
use Cygnite\Console\Command\FormGeneratorCommand;
use Cygnite\Console\Command\ControllerGeneratorCommand;
use Cygnite\Console\Command\ModelGeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

require BASE_PATH.DS.APP_PATH.'/configs/database.php';
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
 * @Package            :  Console
 * @Filename           :  CygniteApplication.php
 * @Description        :  Cygnite Application is the middle ware of all your console command using Cygnite CLI.
 *                        Cygnite Cli driven by Symfony2 Console Component.
 * @Author             :  Sanjoy Dey
 * @Copyright          :  Copyright (c) 2013 - 2014,
 * @Link	           :  http://www.cygniteframework.com
 * @Since	           :  Version 1.0.6
 * @File Source
 *
 */

class CygniteApplication extends Application
{
    private $version;

    private $description;

    public function __construct($version, $description = 'Cygnite CLI Application')
    {
        parent::__construct($description, $version);

        //Get Generator command instance and set table schema
        $generateInstance = GeneratorCommand::instance();
        $generateInstance->setSchema(new Table);

        //Initialise Migration
        $initInstance = InitCommand::instance();
        $initInstance->setSchema(new Table);

        //Get the migration instance and Set schema
        $migrationInstance = MigrationCommand::instance();
        $migrationInstance->setSchema(new Table);
        $migrationInstance->setMigrationPath($this->getApplicationDirectory());

        //Get the Form Generator instance and set Schema
        $formInstance = FormGeneratorCommand::instance();
        $formInstance->setSchema(new Table);

        $controllerInstance = ControllerGeneratorCommand::make();
        $modelInstance = ModelGeneratorCommand::make();
        $modelInstance->setSchema(new Table);


        $this->addCommands(
            array(
                $initInstance,
                $generateInstance,
                $migrationInstance,
                $controllerInstance,
                $formInstance,
                $modelInstance
            )
        );
    }

    // Get the Application directory
    private function getApplicationDirectory()
    {
        return BASE_PATH.DS.APP_PATH;
    }
}
