<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:22
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class TarGz extends AbstractCommand
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
            $this->getProjectName(),
            $this->input
        );

        $directoryName = NameUtil::generateDirectoryName(
            $this->getProjectName(),
            $this->input
        );

        $command = sprintf(
            "cd %s && tar -czf %s %s",
            $this->getWorkingDirectory(),
            $packageName,
            $directoryName
        );

        $this->shellExec($command);
    }
}