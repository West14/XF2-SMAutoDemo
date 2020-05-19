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
        return $this->finder('West\SMAutoDemo:Demo')
            ->order('downloaded_at', 'DESC');
    }

    public function findDemosForView(bool $isDownloaded = true)
    {
        return $this->findDemosForList()
            ->where('download_state', $isDownloaded ? 'downloaded' : 'not_downloaded');
    }

    /**
     * @param int $cutOff
     * @return \XF\Mvc\Entity\Finder
     */
    public function findExpiredDemos(int $cutOff = 0)
    {
        $cutOff = \XF::$time - ($cutOff == 0 ? $this->options()->wsmadDemoLifetime * 3600 : $cutOff);

        return $this->findDemosForList()
            ->where([
                'download_state' => 'downloaded',
                ['downloaded_at', '<', $cutOff]
            ]);
    }
}