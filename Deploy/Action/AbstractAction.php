<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:10
 */

namespace Deploy\Action;

use Deploy\Arguments;
use Deploy\Config;
use Monolog\Logger;

abstract class AbstractAction {

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

    public function __construct(Arguments $arg, Config $config, Logger $logger) {
        $this->logger = $logger;
        $this->arguments = $arg;
        $this->config = $config;
    }

    /**
     * Perform every task before processing the action
     */
    abstract protected function begin();

    /**
     * Tasks of the current action
     */
    abstract protected function processing();

    /**
     * Perform every task after processing the action
     */
    abstract protected function end();

    public function process() {
        $this->begin();
        $this->processing();
        $this->end();
    }
}