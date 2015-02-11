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
            "cd %s && tar -czf %s %s",
            $this->config->getWorkingDirectory(),
            $packageName,
            $this->config->getProject() . '-' . $this->arguments->getRelease()
        );
        $this->logger->debug($command);

        exec($command, $this->output);
    }
}