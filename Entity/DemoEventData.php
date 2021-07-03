<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 14.06.2020
 * Time: 17:38
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string event_id
 * @property string key
 * @property string value
 */
class DemoEventData extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_demo_event_data';
        $structure->shortName = 'West\SMAutoDemo:DemoEventData';
        $structure->primaryKey = ['event_id', 'field'];
        $structure->columns = [
            'event_id' => ['type' => self::STR, 'maxLength' => 36, 'required' => true],
            'field' => ['type' => self::STR, 'maxLength' => 64, 'required' => true],
            'value' => ['type' => self::BINARY, 'maxLength' => 128, 'required' => true]
        ];
        $structure->relations = [
            'Event' => [
                'entity' => 'West\SMAutoDemo:Event',
                'type' => self::TO_ONE,
                'conditions' => 'event_id'
            ]
        ];

        return $structure;
    }
}