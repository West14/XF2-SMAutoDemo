<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 01.06.2020
 * Time: 12:29
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string demo_id
 * @property int account_id
 * @property array data
 *
 * RELATIONS
 * @property \West\SMAutoDemo\Entity\Player Player
 */
class DemoPlayer extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_demo_player';
        $structure->shortName = 'West\SMAutoDemo:DemoPlayer';
        $structure->primaryKey = ['demo_id', 'account_id'];
        $structure->columns = [
            'demo_id' => ['type' => self::STR, 'maxLength' => '36', 'required' => true],
            'account_id' => ['type' => self::UINT, 'required' => true],
            'data' => ['type' => self::JSON_ARRAY, 'default' => []]
        ];

        $structure->relations = [
            'Player' => [
                'entity' => 'West\SMAutoDemo:Player',
                'type' => self::TO_ONE,
                'conditions' => 'account_id'
            ]
        ];
        return $structure;
    }
}