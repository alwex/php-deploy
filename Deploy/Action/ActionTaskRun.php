<?php
/**
 * User: aguidet
 * Date: 13/02/15
 * Time: 10:01
 */

namespace Deploy\Action;

use Deploy\Command\CommandFactory;
use Deploy\Command\CommandFactoryNew;
use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionTaskRun extends AbstractAction
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
                'The environment configuration to use .php-deploy/environment/{env}.yml file',
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
                'The task previously defined in the .php-deploy/environment/{env}.yml file'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfiguration($input, $output);

        /* @var $format DebugFormatterHelper */
        $format = $this->getHelperSet()->get('debug_formatter');

        if ($output->getVerbosity() < OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        }

        $taskName = $input->getArgument('task');

        $commantList = ArrayUtil::getArrayValue($config['tasks'], $taskName, array());
        if (empty($commantList)) {
            throw new \RuntimeException("no command founds for $taskName");
        }

        $hosts = ArrayUtil::getArrayValue($config, 'hosts', array());
        $beforeCommands = ArrayUtil::getArrayValue($config['tasks'][$taskName], 'before', array());
        $preCommands = ArrayUtil::getArrayValue($config['tasks'][$taskName], 'pre', array());
        $postCommands = ArrayUtil::getArrayValue($config['tasks'][$taskName], 'post', array());
        $afterCommands = ArrayUtil::getArrayValue($config['tasks'][$taskName], 'after', array());

        if (count($beforeCommands) > 0) {
            $output->writeln(self::$taskStartTag . "  BEFORE $taskName " . self::$taskEndTag);

            foreach ($beforeCommands as $commandConfiguration) {
                $command = CommandFactoryNew::create(
                    $commandConfiguration,
                    $config,
                    $input,
                    $output,
                    $this
                );

                $command->runCommand();
            }
        }

        if (count($preCommands) > 0) {
            $output->writeln(self::$taskStartTag . "  ON $taskName " . self::$taskEndTag);
            // deployment phase on each host
            foreach ($hosts as $host) {

                $output->writeln(self::$hostStartTag . "  ON $taskName on $host " . self::$hostEndTag);

                foreach ($preCommands as $commandConfiguration) {
                    $command = CommandFactoryNew::create(
                        $commandConfiguration,
                        $config,
                        $input,
                        $output,
                        $this
                    );

                    $command->setCurrentHost($host);

                    $command->runCommand();
                }
            }

        }

        // on-deploy
        // on each host after code has been copied
        if (count($postCommands) > 0) {
            $output->writeln(self::$taskStartTag . "  POST $taskName " . self::$taskEndTag);

            foreach ($hosts as $host) {

                foreach ($postCommands as $commandConfiguration) {

                    $output->writeln(self::$hostStartTag . "  POST $taskName on $host " . self::$hostEndTag);

                    $command = CommandFactoryNew::create(
                        $commandConfiguration,
                        $config,
                        $input,
                        $output,
                        $this
                    );

                    $command->setCurrentHost($host);

                    $command->runCommand();
                }

            }
        }

        // post-release
        // on each host after release has been activated
        if (count($afterCommands) > 0) {
            $output->writeln(self::$taskStartTag . "  AFTER $taskName " . self::$taskEndTag);

            foreach ($afterCommands as $commandConfiguration) {
                $command = CommandFactoryNew::create(
                    $commandConfiguration,
                    $config,
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