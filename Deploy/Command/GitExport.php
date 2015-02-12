<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 11:43
 */

namespace Deploy\Command;


class GitExport extends AbstractCommand {

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
        $this->logger->debug($command);
        exec($command, $this->output);

        // create working directory
        $command = sprintf("mkdir -p %s", $workingDir);
        $this->logger->debug($command);
        exec($command, $this->output);

        $command = sprintf(
            "cd %s && git clone %s %s && cd %s && git checkout -f refs/tags/%s",
            $workingDir,
            $this->config->getVcs(),
            $this->config->getProject() . '-' . $this->arguments->getRelease(),
            $this->config->getProject() . '-' . $this->arguments->getRelease(),
            $this->arguments->getRelease()
        );

        $this->logger->debug($command);

        exec($command, $this->output);
    }
}