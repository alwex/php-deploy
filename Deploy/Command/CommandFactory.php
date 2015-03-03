<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 17:12
 */

namespace Deploy\Command;


use Deploy\Arguments;
use Deploy\Config;
use Deploy\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFactory {

    /**
     * @param $commandName
     * @param Config $config
     * @param Arguments $arguments
     * @param Logger $logger
     * @return AbstractCommand
     */
    public static function create($commandName, Config $config, InputInterface $input, OutputInterface $output, \Symfony\Component\Console\Command\Command $command) {

        if (!class_exists($commandName)) {
            // require the good file
            require_once getcwd() . '/.php-deploy/Command/' . $commandName . '.php';
        }

        return new $commandName($config, $input, $output, $command);
    }

}