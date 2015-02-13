<?php
/**
 * User: aguidet
 * Date: 13/02/15
 * Time: 16:39
 */

namespace Deploy\Action;

use Deploy\Command\CommandFactory;
use Deploy\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionAddEnv extends Command {
    protected function configure()
    {
        $this
            ->setName('action:addenv')
            ->setDescription('Create the default environment configuration file, a .php-deploy/environments/{env}.ini default file will be created')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The environment name to create'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $configurationPath = getcwd() . '/.php-deploy';
        $envPath = $configurationPath . '/environments';
        $env = $input->getArgument('name');

        if (!is_dir($envPath)) {
            $output->writeln("<error>Project has not been initialized, please initialize it !!!</error>");
        } else if (!file_exists($envPath . '/' . $env . '.ini')) {

            $output->writeln("<info>Creating default $env.ini file</info>");

            exec(
                sprintf(
                    "cp %s %s",
                    __DIR__ . '/../../templates/env.ini',
                    $envPath . "/$env.ini"
                )
            );


            $output->writeln("<info>Environment $env added</info>");
            $output->writeln("<info>Please edit .php-deploy/environments/$env.ini</info>");

        } else {

            $output->writeln("<error>Environments already exists !!!</error>");

        }
    }
}