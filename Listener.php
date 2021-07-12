<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.07.2021
 * Time: 20:02
 * Made with <3 by West from Bubuni Team
 */

namespace West\SMAutoDemo;


use XF\Mvc\Entity\Entity;

class Listener
{
    public static function threadFieldValueStructure(\XF\Mvc\Entity\Manager $em, \XF\Mvc\Entity\Structure &$structure)
    {
        $structure->relations += [
            'Thread' => [
                'entity' => 'XF:Thread',
                'type' => Entity::TO_ONE,
                'conditions' => 'thread_id'
            ]
        ];
    }
}