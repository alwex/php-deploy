<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 09:53
 */

namespace Deploy\Action;


use Deploy\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionTaskList extends Command
{

    protected function configure()
    {
        $this
            ->setName('task:list')
            ->setDescription('List the available tasks for the specified environment')

            ->addOption(
                'env',
                null,
                InputOption::VALUE_REQUIRED,
                'The environment configuration to use .php-deploy/environment/{env}.ini file',
                'dev'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');

        $configuration = Config::loadEnv($env);
        $output->writeln("<comment>Available tasks for $env</comment>");

        foreach ($configuration as $key => $value) {
            if (is_array($value) && $key != 'hosts') {
                $output->writeln(" <info>$key</info>");

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    foreach ($value as $stageName => $commandList) {
                        foreach ($commandList as $commandName) {

                            $formatedStageName = str_pad($stageName, 10);
                            $formatedCommandName = str_pad($commandName, 0);

                            $output->writeln("   $formatedStageName  $formatedCommandName");
                        }
                    }
                }
            }

        }
    }
}