<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 12:24
 */

namespace Deploy\Command;

use Deploy\Arguments;
use Deploy\Config;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractCommand
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var Config
     */
    protected $config;

    protected $commandOutput;

    /**
     * @var Command
     */
    protected $command;

    public function __construct(Config $config, InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
        $this->commandOutput = array();
        $this->command = $command;
    }

    public function runCommand()
    {
        // before run
        // if something is going wrong
        // the script end
        if (!$this->input->getOption('dry')) {
            $this->beforeRun();
        }

        $this->run();

        // after run
        // check the post conditions
        // or finalization tasks
        if (!$this->input->getOption('dry')) {
            $this->afterRun();
        }
    }

    /**
     * check the preconditions before running the
     * command. Throw a RuntimeException if
     * something is not ok
     *
     * @throw \RuntimeException
     * @return void
     */
    public function beforeRun()
    {

    }

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public abstract function run();

    /**
     * check if the command has been correctly executed
     * critical commands may be validated before continuing
     *
     * @throw \RuntimeException
     * @return void
     */
    public function afterRun()
    {

    }

    protected function shellExec($command)
    {

        if (!$this->input->getOption('dry')) {

            /* @var $helper ProcessHelper */
            $helper = $this->command->getHelper('process');
            $process = new Process($command);
            $helper->run($this->output, $process);

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

        } else {
            $this->output->writeln("<comment>$command</comment>");
        }

    }

}