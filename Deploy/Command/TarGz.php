<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:22
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class TarGz extends AbstractCommand {

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

        $command = sprintf(
            "tar -czf %s %s",
            $packageName,
            $this->config->getFromDirectory()
        );
        $this->logger->debug($command);

        exec($command, $this->output);
    }
}