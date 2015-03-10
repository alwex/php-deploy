<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 11:43
 */

namespace Deploy\Command;


class GitExport extends AbstractCommand
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

        // clone project
        $command = sprintf(
            "cd %s && git clone %s %s && cd %s && git checkout -f refs/tags/%s",
            $workingDir,
            $this->config->getVcs(),
            $this->config->getProject() . '-' . $this->input->getOption('release'),
            $this->config->getProject() . '-' . $this->input->getOption('release'),
            $this->input->getOption('release')
        );

        $this->shellExec($command);

        // remove .git
        $command = sprintf(
            "cd %s && rm  -rf .git",
            $workingDir . '/' . $this->config->getProject() . '-' . $this->input->getOption('release')
        );

        $this->shellExec($command);
    }

    public function afterRun()
    {

        $workingDir = $this->config->getWorkingDirectory();
        $projectDir = $this->config->getProject() . '-' . $this->input->getOption('release');

        $fullDir = $workingDir . '/' . $projectDir;

        if (!is_dir($fullDir)) {
            throw new \RuntimeException("git export failed, $fullDir has not been created", 400);
        }

    }
}