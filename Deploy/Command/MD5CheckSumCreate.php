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
            $this->config,
            $this->input
        );

        $projectName = $this->config->getProject();

        $command = 'cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum';

        $command = sprintf(
            $command,
            $this->config->getWorkingDirectory() . '/' . $directoryName
        );

        $this->shellExec($command);

        if (!$this->input->getOption('dry')) {
            file_put_contents($this->config->getWorkingDirectory() . '/' . $directoryName . '/CHECKSUM.md5', trim($this->commandOutput));
        }
    }
}