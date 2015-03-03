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

class ActionTask extends Command {

    private static $startTag = "<fg=black;bg=white;>";
    private static $endTag = "</fg=black;bg=white;>";

    protected function configure()
    {
        $this
            ->setName('task:run')
            ->setDescription('Run the specified task')
            ->addOption(
                'release',
                null,
                InputOption::VALUE_OPTIONAL,
                'The version of the application you deal with'
            )
            ->addOption(
                'env',
                null,
                InputOption::VALUE_REQUIRED,
                'The environment configuration to use .php-deploy/environment/{env}.ini file'
            )
            ->addOption(
                'dry',
                null,
                InputOption::VALUE_NONE,
                'Only output the command to execute, nothing will be executed'
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

        if ($output->getVerbosity() < OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        }

        if ($input->getOption('env') == null) {

            throw new \RuntimeException("env option is mandatory");

        } else if (!file_exists(getcwd() . '/.php-deploy/environments/' . $input->getOption('env') . '.ini')) {

            throw new \RuntimeException("env " .  $input->getOption('env') . " is not defined");

        } else {


            $configuration = Config::load($input);
            $taskName = $input->getArgument('task');

            $output->writeln(self::$startTag . "  BEFORE $taskName  " . self::$endTag);

            foreach ($configuration->getPreTaskCommands() as $commandName) {
                $command = CommandFactory::create(
                    $commandName,
                    $configuration,
                    $input,
                    $output,
                    $this
                );

                $command->runCommand();
            }

            $output->writeln(self::$startTag . "  ON $taskName  " . self::$endTag);

            // deployment phase on each host
            foreach ($configuration->getHosts() as $host) {

                $configuration->setCurrentHost($host);

                foreach ($configuration->getOnTaskCommands() as $commandName) {
                    $command = CommandFactory::create(
                        $commandName,
                        $configuration,
                        $input,
                        $output,
                        $this
                    );

                    $command->runCommand();
                }
            }

            // on-deploy
            // on each host after code has been copied
            $output->writeln(self::$startTag . "  POST $taskName  " . self::$endTag);

            foreach ($configuration->getPostTaskCommands() as $commandName) {

                foreach ($configuration->getHosts() as $host) {

                    $configuration->setCurrentHost($host);

                    $command = CommandFactory::create(
                        $commandName,
                        $configuration,
                        $input,
                        $output,
                        $this
                    );

                    $command->runCommand();
                }
            }

            // post-release
            // on each host after release has been activated
            $output->writeln(self::$startTag . "  AFTER $taskName  " . self::$endTag);

            foreach ($configuration->getAfterTaskCommands() as $commandName) {
                $command = CommandFactory::create(
                    $commandName,
                    $configuration,
                    $input,
                    $output,
                    $this
                );

                $command->runCommand();
            }

            $output->writeln(self::$startTag . "  TASK $taskName COMPLETE  " . self::$endTag);
        }
    }

}