<?php
/**
 * User: aguidet
 * Date: 11/02/15
 * Time: 11:14
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class Rm extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $packageName = NameUtil::generatePackageName(
            $this->config,
            $this->arguments
        );

        $command = sprintf(
            "rm %s",
            $packageName
        );

        $this->logger->debug($command);
        exec($command, $this->output);
    }
}