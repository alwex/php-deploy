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

    public static function generatePackageName($projectName, InputInterface $input)
    {
        return self::generateDirectoryName($projectName, $input)
        . '.tar.gz';
    }

    public static function generateDirectoryName($projectName, InputInterface $input)
    {
        return $projectName
        . '-'
        . $input->getOption('release');
    }
}