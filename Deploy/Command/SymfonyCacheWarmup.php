<?php
/**
 * User: aguidet
 * Date: 16/02/15
 * Time: 11:13
 */

namespace Deploy\Command;


class SymfonyCacheWarmup extends AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $command = sprintf(
            'cd %s && php app/console cache:warmup --env=%s --no-debug',
            $this->config->getToDirectory() . '/' . $this->config->getSymlink(),
            $this->input->getOption('env')
        );

        $this->runCommand($command);
    }
}