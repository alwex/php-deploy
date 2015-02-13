<?php
/**
 * User: aguidet
 * Date: 13/02/15
 * Time: 10:01
 */

namespace Deploy\Action;

use Deploy\Command\CommandFactory;
use Deploy\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionExecute extends Command {

    protected function configure()
    {
        $this
            ->setName('action:execute')
            ->setDescription('Execute the specified task')
            ->addOption(
                'release',
                null,
                InputOption::VALUE_REQUIRED,
                'The version of the application you deal with'
            )
            ->addOption(
                'env',
                null,
                InputOption::VALUE_REQUIRED,
                'The environment configuration to use .php-deploy/environment/{env}.ini file'
            )
            ->addArgument(
                'task',
                InputArgument::REQUIRED,
                'The task previously defined in the .php-deploy/environment/{env}.ini file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('release') == null) {

            $output->writeln("<error>release option is mandatory</error>");

        } else if ($input->getOption('env') == null) {

            $output->writeln("<error>env option is mandatory</error>");

        } else if (!file_exists(getcwd() . '/.php-deploy/environments/' . $input->getOption('env') . '.ini')) {

            $output->writeln("<error>env {$input->getOption('env')} does not exists</error>");

        } else {


            $configuration = Config::load($input);
            $taskName = $input->getArgument('task');

            $output->writeln("<info>before $taskName</info>");

            foreach ($configuration->getPreTaskCommands() as $commandName) {
                $command = CommandFactory::create(
                    $commandName,
                    $configuration,
                    $input,
                    $output
                );

                $command->runCommand();
            }

            $output->writeln("<info>on $taskName</info>");

            // deployment phase on each host
            foreach ($configuration->getHosts() as $host) {

                $configuration->setCurrentHost($host);

                foreach ($configuration->getOnTaskCommands() as $commandName) {
                    $command = CommandFactory::create(
                        $commandName,
                        $configuration,
                        $input,
                        $output
                    );

                    $command->runCommand();
                }
            }

            // on-deploy
            // on each host after code has been copied
            $output->writeln("<info>post $taskName</info>");

            foreach ($configuration->getPostTaskCommands() as $commandName) {
                foreach ($configuration->getHosts() as $host) {

                    $configuration->setCurrentHost($host);

                    $command = CommandFactory::create(
                        $commandName,
                        $configuration,
                        $input,
                        $output
                    );

                    $command->runCommand();
                }
            }

            // post-release
            // on each host after release has been activated
            $output->writeln("<info>after $taskName</info>");

            foreach ($configuration->getAfterTaskCommands() as $commandName) {
                $command = CommandFactory::create(
                    $commandName,
                    $configuration,
                    $input,
                    $output
                );

                $command->runCommand();
            }

            $output->writeln("<fg=black;bg=green;>task $taskName complete</fg=black;bg=green>");
        }
    }

}