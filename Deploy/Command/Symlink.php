<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:53
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class Symlink extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $directoryName = NameUtil::generateDirectoryName($this->config, $this->arguments);

        $command = sprintf(
            "ssh %s@%s \"rm %s\"",
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $this->config->getSymlink()
        );

        $this->logger->debug($command);
        exec($command, $this->output);

        $command = sprintf(
            "ssh %s@%s \"ln -s %s %s\"",
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName,
            $this->config->getToDirectory() . '/' . $this->config->getSymlink()
        );

        $this->logger->debug($command);
        exec($command, $this->output);
    }
}