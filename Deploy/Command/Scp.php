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
            "scp %s %s@%s:%s",
            $this->getWorkingDirectory() . '/' . $packageName,
            get_current_user(),
            $this->getCurrentHost(),
            $this->get('destination')
        );

        $this->shellExec($command);
    }
}