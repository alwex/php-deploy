<?php
/**
 * User: aguidet
 * Date: 10/03/15
 * Time: 17:14
 */

namespace Deploy\Action;


use Deploy\Util\ArrayUtil;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class AbstractAction extends Command
{
    public function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        $configPath = getcwd() . '/.php-deploy';
        $configDirectory = array(
            $configPath,
            $configPath . '/environments'
        );

        $locator = new FileLocator($configDirectory);

        $env = $input->getOption('env');

        $envFile = $locator->locate($env . '.yml');

        $configEnv = Yaml::parse($envFile);

        // imports configuration files
        $imports = ArrayUtil::getArrayValue($configEnv, 'imports', array());
        foreach ($imports as $import) {
            $resource = ArrayUtil::getArrayValue($import, 'resource', null);
            if ($resource != null) {
                $importFile = $locator->locate($resource);
                $importConfig = Yaml::parse($importFile);

                // merge tasks
                if (isset($importConfig['tasks'])) {
                    $configEnv['tasks'] += $importConfig['tasks'];
                    unset ($importConfig['tasks']);
                }

                $configEnv = $importConfig + $configEnv;
            }
        }
        unset($configEnv['imports']);

        // replace variables
        $dump = var_export($configEnv, true);

        $mathes = array();
        preg_match_all('/%[A-Za-z-0-9_].*%/', $dump, $matches);

        $vars = ArrayUtil::getArrayValue($matches, 0, array());

        foreach ($vars as $var) {
            $key = str_replace('%', '', $var);
            $dump = str_replace($var, ArrayUtil::getArrayValue($configEnv, $key, '~'), $dump);
        }

        eval('$configEnv = ' . $dump . ';');

        return $configEnv;
    }
}