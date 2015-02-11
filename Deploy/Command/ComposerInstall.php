<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:14
 */

namespace Deploy\Command;


class ComposerInstall extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        exec('composer install', $this->output);
    }
}