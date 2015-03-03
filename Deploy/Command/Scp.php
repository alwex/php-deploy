<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:44
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class Scp extends AbstractCommand
{

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
            $this->input
        );

        $directoryName = NameUtil::generateDirectoryName(
            $this->config,
            $this->input
        );

        $command = sprintf(
            "scp %s %s@%s:%s",
            $this->config->getWorkingDirectory() . '/' . $packageName,
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory()
        );

        $this->shellExec($command);
    }
}