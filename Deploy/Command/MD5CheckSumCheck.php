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
            $this->config,
            $this->input
        );

        $projectName = $this->config->getProject();

        $command = "ssh %s@%s \" cd %s ; find -exec md5sum \"{}\" \\; > /tmp/%s.md5.tmp ; md5sum /tmp/%s.md5.tmp \"";

        $command = sprintf(
            $command,
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName,
            $projectName,
            $projectName,
            $projectName
        );

        $this->shellExec($command);

        $expected = trim(file_get_contents("/tmp/$projectName.md5"));
        $result = trim($this->commandOutput);

        if ($expected != $result) {
            throw new \RuntimeException("extracted code MD5 sum differs from package");
        }
    }
}