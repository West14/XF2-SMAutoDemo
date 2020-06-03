<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 15.04.2020
 * Time: 15:01
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Cron;


class DemoDownload
{
    public static function enqueueDownload()
    {
        /** @var \West\SMAutoDemo\Repository\Demo $demoRepo */
        $demoRepo = \XF::repository('West\SMAutoDemo:Demo');

        /** @var \XF\Mvc\Entity\ArrayCollection|\West\SMAutoDemo\Entity\Demo[] $demos */
        $demos = $demoRepo->findDemosForView(false)
            ->fetch();

        if ($demos->count() > 0)
        {
            \XF::app()
                ->jobManager()
                ->enqueue(
                    'West\SMAutoDemo:DemoDownload',
                    ['demoIds' => $demos->keys()]
                );
        }

        foreach ($demos as $demo)
        {
            $demo->fastUpdate('download_state', 'enqueued');
        }
    }
}