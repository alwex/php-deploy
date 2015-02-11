<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:11
 */

namespace Deploy\Action;


class Init extends AbstractAction {

    /**
     * Perform every task before processing the action
     */
    protected function begin()
    {
        $this->logger->info(sprintf(
            "start initialization of project %s",
            $this->arguments->getProject()
        ));
    }

    /**
     * Tasks of the current action
     */
    protected function processing()
    {
        $this->logger->info(sprintf(
            "processing initialization of project %s",
            $this->arguments->getProject()
        ));

        $this->logger->info(sprintf(
            "creating file %s.ini",
            $this->arguments->getTo()
        ));

        $this->config->init($this->arguments->getTo());
    }

    /**
     * Perform every task after processing the action
     */
    protected function end()
    {
        $this->logger->info(sprintf(
            "end initialization of project %s",
            $this->arguments->getProject()
        ));
    }
}