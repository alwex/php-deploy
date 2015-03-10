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


        $command = sprintf(
            'ssh %s@%s " cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum "',
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName
        );

        $this->shellExec($command);

        $result = trim($this->commandOutput);

        $command = sprintf(
            'ssh %s@%s " cd %s ; cat CHECKSUM.md5 "',
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName
        );

        $this->shellExec($command);

        $expected = trim($this->commandOutput);

        if (!$this->input->getOption('dry')) {
            if ($expected != $result) {
                throw new \RuntimeException("extracted code MD5 sum differs from package expected [$expected] found [$result]");
            }
        }
    }
}