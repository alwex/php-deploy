<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 18:44
 */

namespace Deploy\Util;


use Deploy\Arguments;
use Deploy\Config;
use Symfony\Component\Console\Input\InputInterface;

class NameUtil
{

    public static function generatePackageName(Config $config, InputInterface $input)
    {
        return self::generateDirectoryName($config, $input)
        . '.tar.gz';
    }

    public static function generateDirectoryName(Config $config, InputInterface $input)
    {
        return $config->getProject()
        . '-'
        . $input->getOption('release');
    }
}