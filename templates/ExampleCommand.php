<?php

/**
 * Example Command
 */
class ExampleCommand extends AbstractCommand
{

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $workingDir = $this->config->getWorkingDirectory();

        // clean working directory
        $command = sprintf("rm -rf %s", $workingDir);

        $this->shellExec($command);

        // create working directory
        $command = sprintf("mkdir -p %s", $workingDir);
        $this->shellExec($command);

        $command = sprintf(
            "cd %s && git clone %s %s && cd %s && git checkout -f refs/tags/%s",
            $workingDir,
            $this->config->getVcs(),
            $this->config->getProject() . '-' . $this->input->getOption('release'),
            $this->config->getProject() . '-' . $this->input->getOption('release'),
            $this->input->getOption('release')
        );

        $this->shellExec($command);
    }
}