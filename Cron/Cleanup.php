<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 19.05.2020
 * Time: 23:23
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Cron;


class Cleanup
{
    /**
     * @throws \XF\PrintableException
     */
    public static function performCleanup()
    {
        /** @var \West\SMAutoDemo\Repository\Demo $repo */
        $repo = \XF::repository('West\SMAutoDemo:Demo');

        /** @var \West\SMAutoDemo\Entity\Demo $demo */
        foreach ($repo->findExpiredDemos()->fetch() as $demo)
        {
            $demo->delete();
        }
    }
}