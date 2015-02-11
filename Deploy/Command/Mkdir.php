<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 10:28
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class Mkdir extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $directoryName = NameUtil::generateDirectoryName($this->config, $this->arguments);
        $command = sprintf("ssh %s@%s \"mkdir -p %s\"",
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName
        );

        $this->logger->debug($command);
        exec($command, $this->output);
    }
}