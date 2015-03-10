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

        $command = 'ssh %s@%s " cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum "';

        $command = sprintf(
            $command,
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $directoryName
        );

        $this->shellExec($command);

        if (!$this->input->getOption('dry')) {
            $expected = trim(file_get_contents($this->config->getToDirectory() . '/' . $directoryName . '/CHECKSUM.md5'));
            $result = trim($this->commandOutput);

            if ($expected != $result) {
                throw new \RuntimeException("extracted code MD5 sum differs from package expected [$expected] found [$result]");
            }
        }
    }
}