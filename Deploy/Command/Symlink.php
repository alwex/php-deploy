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
        $directoryName = NameUtil::generateDirectoryName(
            $this->getProjectName(),
            $this->input
        );

        $command = sprintf(
            "ssh %s@%s \"rm %s ; ln -s %s %s\"",
            get_current_user(),
            $this->getCurrentHost(),
            // rm previous link
            $this->get('directory') . '/' . $this->get('symlink'),
            // create new link
            $this->get('directory') . '/' . $directoryName,
            $this->get('directory') . '/' . $this->get('symlink')
        );

        $this->shellExec($command);
    }
}