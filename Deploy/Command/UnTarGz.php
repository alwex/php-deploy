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
        if ($this->get('destination') == null) {
            $class = get_class($this);
            throw new \InvalidArgumentException("destination argument is mandatory for $class command, please check you configuration");
        }

        $packageName = NameUtil::generatePackageName(
            $this->getProjectName(),
            $this->input
        );

        $directoryName = NameUtil::generateDirectoryName(
            $this->getProjectName(),
            $this->input
        );

        $command = sprintf(
            "ssh %s \"cd %s && tar -xzf %s && rm %s\"",
            $this->getCurrentHost(),
            $this->get('destination'),
            $packageName,
            $packageName
        );

        $this->shellExec($command);
    }
}