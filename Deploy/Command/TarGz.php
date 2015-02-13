<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:22
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class TarGz extends AbstractCommand {

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
            "cd %s && tar -czf %s %s",
            $this->config->getWorkingDirectory(),
            $packageName,
            $directoryName
        );

        $this->shellExec($command);
    }
}