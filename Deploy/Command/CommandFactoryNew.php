<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 17:12
 */

namespace Deploy\Command;


use Deploy\Command;
use Deploy\Config;
use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFactoryNew
{

    /**
     * @param $commandName
     * @param Config $config
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param \Symfony\Component\Console\Command\Command $command
     * @return AbstractCommand
     */
    public static function create($commandConfiguration, array $config, InputInterface $input, OutputInterface $output, \Symfony\Component\Console\Command\Command $command)
    {
        $params = array();

        if (is_array($commandConfiguration)) {
            reset($commandConfiguration);
            $commandName = key($commandConfiguration);
            $params = $commandConfiguration[$commandName];
        } else {
            $commandName = $commandConfiguration;
        }

        if (!class_exists($commandName)) {
            // require the good file
            $commandPath = getcwd() . '/.php-deploy/Command';

            if (!is_dir($commandPath)) {
                $commandPath = '/etc/php-deploy/Command';
            }

            require_once $commandPath . '/' . $commandName . '.php';
        }

        /* @var $command \Deploy\Command\AbstractCommand */
        $command = new $commandName($config, $input, $output, $command);
        $command->setWorkingDirectory(ArrayUtil::getArrayValue($config, 'working_directory'));
        $command->setProjectName(ArrayUtil::getArrayValue($config, 'project_name'));

        foreach ($params as $key => $value) {
            $command->setParam($key, $value);
        }

        return $command;
    }

}