<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:54
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class UnTarGz extends AbstractCommand
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
            "ssh %s@%s \"cd %s && tar -xzf %s && rm %s\"",
            get_current_user(),
            $this->getCurrentHost(),
            $this->get('directory'),
            $packageName,
            $packageName
        );

        $this->shellExec($command);
    }
}