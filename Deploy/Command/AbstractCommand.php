<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 12:24
 */

namespace Deploy\Command;

use Deploy\Arguments;
use Deploy\Config;
use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractCommand
{

    protected $workingDirectory;
    protected $projectName;
    protected $currentHost;

    /**
     * @var array
     */
    protected $params = array();

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

    public function __construct(array $config, InputInterface $input, OutputInterface $output, Command $command)
    {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
        $this->commandOutput = array();
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getCurrentHost()
    {
        return $this->currentHost;
    }

    /**
     * @param mixed $currentHost
     */
    public function setCurrentHost($currentHost)
    {
        $this->currentHost = $currentHost;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return mixed
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * @param mixed $workingDirectory
     */
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function isDry()
    {
        return $this->input->getOption('dry');
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function get($key)
    {
        return ArrayUtil::getArrayValue($this->params, $key, null);
    }

    public function runCommand()
    {
        // before run
        // if something is going wrong
        // the script end
        $this->beforeRun();
        // run the command
        $this->run();
        // after run
        // check the post conditions
        // or finalization tasks
        $this->afterRun();
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

            $this->commandOutput = $process->getOutput();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

        } else {
            /* @var $format DebugFormatterHelper */
            $format = $this->command->getHelperSet()->get('debug_formatter');
            $this->output->write($format->start(spl_object_hash($this), $command, 'DRY'));
        }

    }

}