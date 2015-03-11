<?php
/**
 * User: aguidet
 * Date: 09/03/15
 * Time: 11:13
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class MD5CheckSumCreate extends AbstractCommand
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

        $command = 'cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum';

        $command = sprintf(
            $command,
            $this->getWorkingDirectory() . '/' . $directoryName
        );

        $this->shellExec($command);

        if (!$this->isDry()) {
            file_put_contents($this->getWorkingDirectory() . '/' . $directoryName . '/CHECKSUM.md5', trim($this->commandOutput));
        }
    }
}