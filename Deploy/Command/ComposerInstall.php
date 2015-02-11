<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:14
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class ComposerInstall extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {

        $command = sprintf(
            "cd %s && composer install",
            $this->config->getWorkingDirectory() . '/' . $this->config->getProject() . '-' . $this->arguments->getRelease()
        );

        exec($command, $this->output);
    }
}