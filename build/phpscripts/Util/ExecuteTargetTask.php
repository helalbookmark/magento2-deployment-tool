<?php

/**
 * ExecuteTargetTask
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */
require_once "phing/Task.php";

class ExecuteTargetTask extends Task
{
    /**
     * name of the current target to execute
     *
     * @var string
     */
    protected $target = null;

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Main
     */
    public function main()
    {
        try {
            $message = sprintf('Executing target %s', $this->target);
            $this->log($message);
            $this->getProject()->executeTarget($this->target);
        } catch (BuildException $ex) {
            $message = sprintf('Failed to execute target %s. Reason: %s', $this->target, $ex->getMessage());
            $this->log($message, Project::MSG_ERR);
        }
    }
}