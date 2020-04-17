<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 1:03
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use League\Flysystem\Sftp\SftpAdapter;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int server_id
 * @property string name
 * @property string ip
 * @property int port
 * @property array sftp
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\West\SMAutoDemo\Entity\Demo[] Demos
 */
class Server extends Entity
{
    public function getAddress()
    {
        return "{$this->ip}:{$this->port}";
    }

    public function getFsPrefix()
    {
        return "wsmad-server-{$this->server_id}";
    }

    public function mountFs(\League\Flysystem\MountManager $fs = null)
    {
        /*
        $fs = $this->app()->fs();
        try {
            return $fs->getFilesystem($this->getFsPrefix());
        }
        catch (FilesystemNotFoundException $e)
        {
            return $fs->mountFilesystem(
                $this->getFsPrefix(),
                new \League\Flysystem\Filesystem(
                    new SftpAdapter($this->sftp)
                )
            )->getFilesystem($this->getFsPrefix());
        }*/

        if (!$fs)
        {
            $fs = $this->app()->fs();
        }

        $fs->mountFilesystem(
            $this->getFsPrefix(),
            new \League\Flysystem\Filesystem(
                new SftpAdapter($this->sftp)
            )
        );
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_server';
        $structure->shortName = 'West\SMAutoDemo:Server';
        $structure->primaryKey = 'server_id';
        $structure->columns = [
            'server_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'name' => ['type' => self::STR, 'required' => true],
            'ip' => ['type' => self::STR, 'default' => '127.0.0.1'],
            'port' => ['type' => self::UINT, 'default' => 27015],
            'sftp' => ['type' => self::JSON_ARRAY, 'default' => []]
        ];

        $structure->relations = [
            'Demos' => [
                'entity' => 'West\SMAutoDemo:Demo',
                'type' => self::TO_MANY,
                'conditions' => 'server_id'
            ]
        ];

        return $structure;
    }
}