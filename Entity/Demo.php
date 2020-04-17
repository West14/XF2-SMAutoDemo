<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 0:51
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string demo_id
 * @property int server_id
 * @property array demo_data
 * @property string download_state
 * @property int downloaded_at
 *
 * RELATIONS
 * @property \West\SMAutoDemo\Entity\Server Server
 */
class Demo extends Entity
{
    public function getAbstractedPath()
    {
        return "data://wsmad-demos/{$this->server_id}/{$this->demo_id}";
    }

    public function getAbstractedZipPath()
    {
        return $this->getAbstractedPath() . '.zip';
    }
    
    public function getAbstractedJsonPath()
    {
        return $this->getAbstractedPath() . '.json';
    }

    public function isDownloaded()
    {
        return $this->download_state == 'downloaded';
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_demo';
        $structure->shortName = 'West\SMAutoDemo:Demo';
        $structure->primaryKey = 'demo_id';
        $structure->columns = [
            'demo_id' => ['type' => self::STR, 'maxLength' => 36, 'required' => true],
            'server_id' => ['type' => self::UINT, 'required' => true],
            'demo_data' => ['type' => self::JSON_ARRAY, 'default' => []],
            'download_state' => [
                'type' => self::STR, 'default' => 'not_downloaded',
                'allowedValues' => ['downloaded', 'enqueued', 'not_downloaded']
            ],
            'downloaded_at' => ['type' => self::UINT, 'default' => 0]
        ];

        $structure->relations = [
            'Server' => [
                'entity' => 'West\SMAutoDemo:Server',
                'type' => self::TO_ONE,
                'conditions' => 'server_id'
            ]
        ];

        return $structure;
    }
}