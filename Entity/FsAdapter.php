<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 07.06.2020
 * Time: 21:46
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string adapter_id
 * @property string adapter_class
 * @property string addon_id
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 */
class FsAdapter extends Entity
{
    public function getTitle()
    {
        return \XF::phrase('wsmad_fsadapter_title.' . $this->adapter_id);
    }
    
    protected function _setupDefaults()
    {
        $this->addon_id = $this->em()->getRepository('XF:AddOn')->getDefaultAddOnId();
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_fsadapter';
        $structure->shortName = 'West\SMAutoDemo:FsAdapter';
        $structure->primaryKey = 'adapter_id';
        $structure->columns = [
            'adapter_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
            'adapter_class' => ['type' => self::STR, 'required' => true],
            'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
        ];

        $structure->relations = [
            'AddOn' => [
                'entity' => 'XF:AddOn',
                'type' => self::TO_ONE,
                'conditions' => 'addon_id',
                'primary' => true
            ]
        ];

        return $structure;
    }
}