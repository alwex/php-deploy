<?php

use Deploy\Command\AbstractCommand;

/**
 * Example Command
 */
class {COMMAND_NAME}Command extends AbstractCommand
{

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
        $this->shellExec("figlet before {COMMAND_NAME}");
    }

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $this->shellExec("figlet running {COMMAND_NAME}");
    }

    /**
     * check if the command has been correctly executed
     * critical commands may be validated before continuing
     *
     * @throw \RuntimeException
     * @return void
     */
    public function afterRun()
    {
        $this->shellExec("figlet after {COMMAND_NAME}");
    }
}