<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 16:50
 */

namespace Deploy\Util;


class DirectoryUtil {

    public static function getConfigPath()
    {
        $configurationPath = getcwd() . '/.php-deploy';
        if (! is_dir(getcwd() . '/.php-deploy')) {
            $configurationPath = '/etc/php-deploy';
        }

        return $configurationPath;
    }

    public static function getEnvPath()
    {
        $configurationPath = self::getConfigPath();
        return $configurationPath . '/environments';
    }
}