<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 15.06.2020
 * Time: 14:40
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Util;


class Uuid
{
    /**
     * Copied from
     * https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * Since XF has compat package for random_int we can safely use it here instead of mt_rand
     *
     * @return string
     * @throws \Exception
     */
    public static function generatev4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            random_int(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            random_int(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            random_int(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            random_int(0, 0xffff), mt_rand(0, 0xffff ), mt_rand(0, 0xffff)
        );
    }
}