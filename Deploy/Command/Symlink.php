<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:53
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class Symlink extends AbstractCommand
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
            throw new InvalidArgumentException("destination argument is mandatory for $class command, please check you configuration");
        }

        $directoryName = NameUtil::generateDirectoryName(
            $this->getProjectName(),
            $this->input
        );

        $command = sprintf(
            "ssh %s \"rm %s ; ln -s %s %s\"",
            $this->getCurrentHost(),
            // rm previous link
            $this->get('destination') . '/' . $this->get('symlink'),
            // create new link
            $this->get('destination') . '/' . $directoryName,
            $this->get('destination') . '/' . $this->get('symlink')
        );

        $this->shellExec($command);
    }
}