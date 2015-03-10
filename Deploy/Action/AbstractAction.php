<?php
/**
 * User: aguidet
 * Date: 10/03/15
 * Time: 17:14
 */

namespace Deploy\Action;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class AbstractAction extends Command
{
    public function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        $configDirectory = array(getcwd() . '/.php-deploy/environments');
        $locator = new FileLocator($configDirectory);

        $env = $input->getOption('env');

        $envFile = $locator->locate($env . '.yml');

        $loader = new Yaml();
        $config = $loader->parse($envFile);

        return $config;
    }
}