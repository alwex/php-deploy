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
        if ($this->get('vcs') == null) {
            $class = get_class($this);
            throw new \InvalidArgumentException("vcs argument is mandatory for $class command, please check you configuration");
        }

        $workingDir = $this->getWorkingDirectory();

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
            $this->get('vcs'),
            $this->getProjectName() . '-' . $this->input->getOption('release'),
            $this->getProjectName() . '-' . $this->input->getOption('release'),
            $this->input->getOption('release')
        );

        $this->shellExec($command);

        // remove .git
        $command = sprintf(
            "cd %s && rm -rf .git",
            $workingDir . '/' . $this->getProjectName() . '-' . $this->input->getOption('release')
        );

        $this->shellExec($command);
    }

    public function afterRun()
    {
        if (!$this->isDry()) {
            $workingDir = $this->getWorkingDirectory();
            $projectDir = $this->getProjectName() . '-' . $this->input->getOption('release');

            $fullDir = $workingDir . '/' . $projectDir;

            if (!is_dir($fullDir)) {
                throw new \RuntimeException("git export failed, $fullDir has not been created", 400);
            }
        }
    }
}