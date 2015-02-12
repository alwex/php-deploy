<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 17:12
 */

namespace Deploy\Command;


use Deploy\Arguments;
use Deploy\Config;
use Monolog\Logger;
use Deploy\Command;

class CommandFactory {

    /**
     * @param $commandName
     * @param Config $config
     * @param Arguments $arguments
     * @param Logger $logger
     * @return AbstractCommand
     */
    public static function create($commandName, Config $config, Arguments $arguments, Logger $logger) {

        if (!class_exists($commandName)) {
            // require the good file
            require_once getcwd() . '/.php-deploy/Command/' . $commandName . '.php';
        }

        return new $commandName($config, $arguments, $logger);
    }

}