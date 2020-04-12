<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 17:30
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Repository;


use XF\Mvc\Entity\Repository;

class Server extends Repository
{
    public function findServersForList()
    {
        return $this->finder('West\SMAutoDemo:Server');
    }
}