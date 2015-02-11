<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 12:35
 */

namespace Deploy;


class Arguments {

    // available actions
    const ACTION_DEPLOY = 'deploy';
    const ACTION_ROLLBACK = 'rollback';
    const ACTION_INIT = 'init';

    // available parameters
    const PARAM_TO = 'to';
    const PARAM_RELEASE = 'release';
    const PARAM_PROJECT = 'project';

    private $action;
    private $to;
    private $release;
    private $project;

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getRelease()
    {
        return $this->release;
    }

    /**
     * @param mixed $release
     */
    public function setRelease($release)
    {
        $this->release = $release;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

}