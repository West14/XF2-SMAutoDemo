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
 * @property string adapter_id
 * @property array adapter_options
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\West\SMAutoDemo\Entity\Demo[] Demos
 * @property \West\SMAutoDemo\Entity\FsAdapter FsAdapter
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

    /**
     * @param \League\Flysystem\MountManager|null $fs
     * @throws \Exception
     */
    public function mountFs(\League\Flysystem\MountManager $fs = null)
    {
        if (!$fs)
        {
            $fs = $this->app()->fs();
        }

        $fs->mountFilesystem(
            $this->getFsPrefix(),
            new \League\Flysystem\Filesystem(
                $this->getFsAdapter()->getAdapter()
            )
        );
    }

    /**
     * @return \West\SMAutoDemo\FsAdapter\AbstractAdapter|null
     * @throws \Exception
     */
    public function getFsAdapter()
    {
        $class = \XF::stringToClass($this->FsAdapter->adapter_class, '%s\FsAdapter\%s');

        if (!class_exists($class))
            return null;

        $class = \XF::extendClass($class);
        return new $class($this->adapter_id, $this->adapter_options);
    }

    /**
     * @throws \Exception
     */
    protected function _preSave()
    {
        $errors = $this->getFsAdapter()->validateOptions();
        foreach ($errors as $error)
        {
            $this->error($error);
        }
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
            'adapter_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
            'adapter_options' => ['type' => self::JSON_ARRAY, 'default' => []],
        ];

        $structure->relations = [
            'Demos' => [
                'entity' => 'West\SMAutoDemo:Demo',
                'type' => self::TO_MANY,
                'conditions' => 'server_id',
                'cascadeDelete' => true
            ],
            'FsAdapter' => [
                'entity' => 'West\SMAutoDemo:FsAdapter',
                'type' => self::TO_ONE,
                'conditions' => 'adapter_id'
            ]
        ];

        return $structure;
    }
}