<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:13
 */

namespace Deploy\Action;


use Deploy\Command\CommandFactory;
use Deploy\Command\ComposerInstall;
use Deploy\Command\GitExport;
use Deploy\Command\Mkdir;
use Deploy\Command\Rm;
use Deploy\Command\Scp;
use Deploy\Command\Symlink;
use Deploy\Command\TarGz;
use Deploy\Command\UnTarGz;

class Deploy extends AbstractAction {

    /**
     * Perform every task before processing the action
     */
    protected function begin()
    {
        // TODO: Implement begin() method.
    }

    /**
     * Tasks of the current action
     */
    protected function processing()
    {
        // pre-deploy
        // git clone
        // composer install etc ...
        $this->logger->addInfo("pre deploy");

        foreach ($this->config->getPreDeployCommands() as $commandName) {
            $command = CommandFactory::create(
                $commandName,
                $this->config,
                $this->arguments,
                $this->logger
            );

            $command->runCommand();
        }

        $this->logger->addInfo("on deploy");
        // deployment phase on each host
        foreach ($this->config->getHosts() as $host) {

            $this->config->setCurrentHost($host);

            foreach ($this->config->getOnDeployCommands() as $commandName) {
                $command = CommandFactory::create(
                    $commandName,
                    $this->config,
                    $this->arguments,
                    $this->logger
                );

                $command->runCommand();
            }
        }

        // on-deploy
        // on each host after code has been copied
        $this->logger->addInfo("post deploy");

        foreach($this->config->getPostDeployCommands() as $commandName) {
            foreach ($this->config->getHosts() as $host) {

                $this->config->setCurrentHost($host);

                $command = CommandFactory::create(
                    $commandName,
                    $this->config,
                    $this->arguments,
                    $this->logger
                );

                $command->runCommand();
            }
        }


        // post-release
        // on each host after release has been activated
        $this->logger->addInfo("post release");

        // post-deploy
        // after deployment complete
        $this->logger->addInfo("after release");
    }

    /**
     * Perform every task after processing the action
     */
    protected function end()
    {
        // TODO: Implement end() method.
    }
}