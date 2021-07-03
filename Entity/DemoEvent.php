<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 14.06.2020
 * Time: 16:47
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string event_id
 * @property string demo_id
 * @property string name
 * @property int time
 * @property int tick
 *
 * RELATIONS
 * @property Demo Demo
 */
class DemoEvent extends Entity
{
    /**
     * @throws \Exception
     */
    protected function _preSave()
    {
        if (!$this->event_id)
            $this->event_id = \West\SMAutoDemo\Util\Uuid::generatev4();
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_demo_event';
        $structure->shortName = 'West\SMAutoDemo:DemoEvent';
        $structure->primaryKey = 'event_id';
        $structure->columns = [
            'event_id' => ['type' => self::STR, 'maxLength' => 36, 'required' => true],
            'demo_id' => ['type' => self::STR, 'maxLength' => 36, 'required' => true],
            'name' => ['type' => self::STR, 'maxLength' => 64, 'required' => true],
            'time' => ['type' => self::UINT, 'required' => true],
            'tick' => ['type' => self::UINT, 'required' => true],
        ];
        $structure->relations = [
            'Demo' => [
                'entity' => 'West\SMAutoDemo:Demo',
                'type' => self::TO_ONE,
                'conditions' => 'demo_id'
            ],
            'Data' => [
                'entity' => 'West\SMAutoDemo:DemoEventData',
                'type' => self::TO_MANY,
                'conditions' => 'event_id',
                'key' => 'field',
                'cascadeDelete' => true
            ]
        ];

        return $structure;
    }
}