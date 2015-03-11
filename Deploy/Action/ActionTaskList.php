<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 09:53
 */

namespace Deploy\Action;


use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionTaskList extends AbstractAction
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $configuration = $this->loadConfiguration($input, $output);

        $env = $input->getOption('env');
        $output->writeln("<comment>Available tasks for $env</comment>");

        $tasks = ArrayUtil::getArrayValue($configuration, 'tasks', array());

        foreach ($tasks as $taskName => $taskConfiguration) {
            $output->writeln("");
            $output->writeln("<info>$taskName</info>");

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                foreach ($taskConfiguration as $stageName => $commandList) {
                    $output->writeln("  $stageName:");

                    foreach ($commandList as $commandName) {

                        if (is_array($commandName)) {
                            reset($commandName);
                            $commandName = key($commandName);
                        }


                        $formatedCommandName = str_pad($commandName, 0);

                        $output->writeln("    $formatedCommandName");
                    }
                }
            }

        }
        $output->writeln("");
    }
}