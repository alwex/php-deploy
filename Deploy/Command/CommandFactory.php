<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 17:12
 */

namespace Deploy\Command;


use Deploy\Arguments;
use Deploy\Command;
use Deploy\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFactory
{

    /**
     * @param $commandName
     * @param Config $config
     * @param Arguments $arguments
     * @param Logger $logger
     * @return AbstractCommand
     */
    public static function create($commandName, Config $config, InputInterface $input, OutputInterface $output, \Symfony\Component\Console\Command\Command $command)
    {
        if (!class_exists($commandName)) {
            // require the good file
            $commandPath = getcwd() . '/.php-deploy/Command';

            if (!is_dir($commandPath)) {
                $commandPath = '/etc/php-deploy/Command';
            }

            require_once $commandPath . '/' . $commandName . '.php';
        }

        $command = new $commandName($config, $input, $output, $command);

        return $command;
    }

}