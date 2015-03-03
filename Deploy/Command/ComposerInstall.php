<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:14
 */

namespace Deploy\Command;

class ComposerInstall extends AbstractCommand
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
            "cd %s && composer install --optimize-autoloader",
            $this->config->getWorkingDirectory() . '/' . $this->config->getProject() . '-' . $this->input->getOption('release')
        );
        $this->shellExec($command);
    }

    public function afterRun()
    {
        $dir = $this->config->getWorkingDirectory() . '/' . $this->config->getProject() . '-' . $this->input->getOption('release');
        $vendorDir = $dir . '/vendor';
        if (!is_dir($vendorDir)) {
            throw new \RuntimeException("composer install failed, $vendorDir has not been created");
        }
    }
}