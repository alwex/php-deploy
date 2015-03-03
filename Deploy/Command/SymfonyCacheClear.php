<?php
/**
 * User: aguidet
 * Date: 16/02/15
 * Time: 11:12
 */

namespace Deploy\Command;


class SymfonyCacheClear extends AbstractCommand
{

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $command = sprintf(
            'ssh %s@%s \"cd %s && php app/console cache:clear --env=%s --no-debug\"',
            $this->config->getLogin(),
            $this->config->getCurrentHost(),
            $this->config->getToDirectory() . '/' . $this->config->getSymlink(),
            $this->input->getOption('env')
        );

        $this->shellExec($command);
    }
}