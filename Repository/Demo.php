<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.04.2020
 * Time: 17:08
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Repository;


use XF\Mvc\Entity\Repository;

class Demo extends Repository
{
    public function findDemosForList()
    {
        return $this->finder('West\SMAutoDemo:Demo');
    }

    public function findDemosForView($isDownloaded = true)
    {
        return $this->findDemosForList()
            ->where('download_state', $isDownloaded ? 'downloaded' : 'not_downloaded');
    }
}