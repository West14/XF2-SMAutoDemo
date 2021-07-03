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
     * @param int $maxRunTime
     * @return JobResult
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \XF\PrintableException
     */
    public function run($maxRunTime)
    {
        if (empty($this->data['demoId']))
        {
            return $this->complete();
        }

        $demo = $this->app->find('West\SMAutoDemo:Demo', $this->data['demoId']);
        if ($demo)
        {
            /** @var \West\SMAutoDemo\Service\Demo\Download $service */
            $service = $this->app->service('West\SMAutoDemo:Demo\Download', $demo);
            $service->download();
        }

        return $this->complete();
    }

    public function getStatusMessage()
    {
        return \XF::phrase('wsmad_downloading_demos...');
    }

    public function canCancel()
    {
        return false;
    }

    public function canTriggerByChoice()
    {
        return false;
    }
}