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
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionTaskRun extends Command
{

    private static $taskStartTag = "<fg=black;bg=white;>";
    private static $taskEndTag = "</fg=black;bg=white;>";
    private static $hostStartTag = "<fg=yellow;bg=black;>";
    private static $hostEndTag = "</fg=yellow;bg=black;>";

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
                'The environment configuration to use .php-deploy/environment/{env}.ini file',
                'dev'
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /* @var $format DebugFormatterHelper */
        $format = $this->getHelperSet()->get('debug_formatter');

        if ($output->getVerbosity() < OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        }


        $configuration = Config::load($input);
        $taskName = $input->getArgument('task');

        if (count($configuration->getPreTaskCommands()) > 0) {
            $output->writeln(self::$taskStartTag . "  BEFORE $taskName " . self::$taskEndTag);

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
        }

        if (count($configuration->getOnTaskCommands()) > 0) {
            $output->writeln(self::$taskStartTag . "  ON $taskName " . self::$taskEndTag);

            // deployment phase on each host
            foreach ($configuration->getHosts() as $host) {

                $output->writeln(self::$hostStartTag . "  ON $taskName on $host " . self::$hostEndTag);

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

        }

        // on-deploy
        // on each host after code has been copied
        if (count($configuration->getPostTaskCommands()) > 0) {
            $output->writeln(self::$taskStartTag . "  POST $taskName " . self::$taskEndTag);

            foreach ($configuration->getPostTaskCommands() as $commandName) {

                foreach ($configuration->getHosts() as $host) {

                    $output->writeln(self::$hostStartTag . "  POST $taskName on $host " . self::$hostEndTag);

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
        }

        // post-release
        // on each host after release has been activated
        if (count($configuration->getAfterTaskCommands()) > 0) {
            $output->writeln(self::$taskStartTag . "  AFTER $taskName " . self::$taskEndTag);

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
        }

        $output->writeln(self::$taskStartTag . "  COMPLETE $taskName " . self::$taskEndTag);
    }

}