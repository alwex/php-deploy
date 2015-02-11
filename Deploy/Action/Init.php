<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:11
 */

namespace Deploy\Action;


use Deploy\Arguments;

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
        if ($this->arguments->getTo() == null || $this->arguments->getProject() == null) {
            throw new \RuntimeException("parameter 'to' and 'project' are mandatory");
        }

        $this->logger->info(sprintf(
            "processing initialization of project %s",
            $this->arguments->getProject()
        ));

        $this->logger->info(sprintf(
            "creating file %s.ini",
            $this->arguments->getTo()
        ));

        $this->config->init($this->arguments);
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