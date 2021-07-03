<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 03.07.2021
 * Time: 16:27
 * Made with <3 by West from Bubuni Team
 */

namespace West\SMAutoDemo\Validator;


use XF\Validator\AbstractValidator;

class Demo extends AbstractValidator
{
    public function coerceValue($value)
    {
        return trim($value);
    }

    public function isValid($value, &$errorKey = null)
    {
        $demo = $this->app->find('West\SMAutoDemo:Demo', $value);
        if (!$demo)
        {
            $errorKey = 'wsmad_specified_demo_was_not_found';
            return false;
        }

        return true;
    }
}