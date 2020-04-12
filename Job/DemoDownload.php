<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 19:41
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Job;


use XF\Job\AbstractJob;
use XF\Job\JobResult;

class DemoDownload extends AbstractJob
{
    /**
     * @inheritDoc
     */
    public function run($maxRunTime)
    {
        // TODO: Implement run() method.
    }

    public function getStatusMessage()
    {
        // TODO: Implement getStatusMessage() method.
    }

    public function canCancel()
    {
        // TODO: Implement canCancel() method.
    }

    public function canTriggerByChoice()
    {
        // TODO: Implement canTriggerByChoice() method.
    }
}