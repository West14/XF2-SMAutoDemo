<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.07.2021
 * Time: 17:31
 * Made with <3 by West from Bubuni Team
 */

namespace West\SMAutoDemo\Option;


use XF\Option\AbstractOption;

class DemoHold extends AbstractOption
{
    public static function verifyOption(array &$value, \XF\Entity\Option $option)
    {
        if ($value['enabled'] ?? false)
        {
            if (!\XF::app()->find('XF:ThreadField', $value['customFieldId'] ?? null))
            {
                $option->error(\XF::phrase('wsmad_please_enter_valid_thread_field_id'));
                return false;
            }
        }

        return true;
    }
}