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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand {

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

    public function __construct(Config $config, InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
        $this->commandOutput = array();
    }

    public function runCommand() {
        $this->run();
    }

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public abstract function run();

    protected function shellExec($command) {
        $this->output->writeln("<comment>$command</comment>");

        if (! $this->input->getOption('dry')) {
            exec($command, $this->commandOutput);

            foreach ($this->commandOutput as $line) {
                $this->output->writeln($line);
            }
        }

    }

}