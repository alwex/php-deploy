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

        $command = 'cd %s ; find -exec md5sum "{}" \; > /tmp/%s.md5.tmp ; md5sum /tmp/%s.md5.tmp > /tmp/%s.md5';

        $command = sprintf(
            $command,
            $this->config->getWorkingDirectory() . '/' . $directoryName,
            $projectName,
            $projectName,
            $projectName
        );

        $this->shellExec($command);
    }
}