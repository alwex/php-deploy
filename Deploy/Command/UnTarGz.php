<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:54
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class UnTarGz extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $packageName = NameUtil::generatePackageName(
            $this->config,
            $this->arguments
        );

        $directoryName = NameUtil::generateDirectoryName(
            $this->config,
            $this->arguments
        );

        $command = sprintf(
            "ssh %s@%s \"cd %s && tar -xzf %s && rm %s\"",
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory(),
            $packageName,
            $packageName
        );

        $this->logger->debug($command);
        exec($command, $this->output);
    }
}