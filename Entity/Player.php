<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 01.06.2020
 * Time: 13:03
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use Kruzya\SteamIdConverter\SteamID;

/**
 * COLUMNS
 * @property int account_id
 * @property string username
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class Player extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_player';
        $structure->shortName = 'West\SMAutoDemo:Player';
        $structure->primaryKey = 'account_id';
        $structure->columns = [
            'account_id' => ['type' => self::UINT, 'required' => true],
            'username' => ['type' => self::STR, 'required' => true]
        ];

        $structure->getters = [
            'User' => true
        ];

        return $structure;
    }

    /**
     * @return Entity|null
     * @throws \Kruzya\SteamIdConverter\Exception\InvalidSteamIdException
     */
    public function getUser()
    {
        return $this->em()->findOne('XF:UserConnectedAccount', [
            'provider' => 'steam',
            'provider_key' => (new SteamID($this->account_id))->communityId()
        ]);
    }
}