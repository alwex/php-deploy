<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:44
 */

namespace Deploy\Util;


use Deploy\Arguments;
use Deploy\Config;

class NameUtil {

    public static function generatePackageName(Config $config, Arguments $arguments) {
        return self::generateDirectoryName($config, $arguments)
        . '.tar.gz';
    }

    public static function generateDirectoryName(Config $config, Arguments $arguments) {
        return $config->getProject()
        . '-'
        . $arguments->getRelease();
    }
}