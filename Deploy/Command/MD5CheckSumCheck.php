<?php
/**
 * User: aguidet
 * Date: 09/03/15
 * Time: 11:13
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class MD5CheckSumCheck extends AbstractCommand
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
            'ssh %s@%s "cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum"',
            get_current_user(),
            $this->getCurrentHost(),
            $this->get('directory') . '/' . $directoryName
        );

        $this->shellExec($command);

        $found = $this->commandOutput;

        $command = sprintf(
            'ssh %s@%s "cd %s ; cat CHECKSUM.md5"',
            get_current_user(),
            $this->getCurrentHost(),
            $this->get('directory') . '/' . $directoryName
        );

        $this->shellExec($command);

        $expected = $this->commandOutput;

        if (!$this->isDry()) {

            $found = trim($found);
            $expected = trim($expected);

            if ($expected != $found) {
                throw new \RuntimeException("extracted code MD5 sum differs from package expected [$expected] found [$found]");
            }
        }
    }
}