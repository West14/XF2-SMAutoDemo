<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 08.06.2020
 * Time: 19:35
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\FsAdapter;


class Ftp extends AbstractAdapter
{
    public function getAdapter(): \League\Flysystem\Adapter\AbstractAdapter
    {
        return new \League\Flysystem\Adapter\Ftp($this->options);
    }
}