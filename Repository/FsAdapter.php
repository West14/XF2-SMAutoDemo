<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 08.06.2020
 * Time: 16:57
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Repository;


use XF\Mvc\Entity\Repository;

class FsAdapter extends Repository
{
    public function findAdaptersForList()
    {
        return $this->finder('West\SMAutoDemo:FsAdapter')
            ->with('AddOn')
            ->where('AddOn.active', true);
    }
}