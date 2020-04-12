<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 31.03.2020
 * Time: 0:28
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Util;


class Path
{
    public static function buildPathFromRoot(array $parts)
    {
        return self::buildPath(array_merge([\XF::getRootDirectory()], $parts));
    }

    public static function buildPath(array $parts)
    {
        $path = '';
        foreach ($parts as $part)
        {
            $path .= \XF::$DS . $part;
        }

        return $path;
    }
}