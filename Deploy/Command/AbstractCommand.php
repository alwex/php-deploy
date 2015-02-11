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

abstract class AbstractCommand {

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Arguments
     */
    protected $arguments;

    /**
     * @var Config
     */
    protected $config;

    protected $output = array();

    public function __construct(Config $config, Arguments $arguments, Logger $logger) {
        $this->logger = $logger;
        $this->config = $config;
        $this->arguments = $arguments;
    }

    public function runCommand() {
        $this->run();
        $this->processOutput();
    }

    /**
     * process command output to show it to the user
     * or to log it
     */
    private function processOutput() {
        foreach ($this->output as $line) {
            $this->logger->info($line);
        }
    }

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public abstract function run();

}