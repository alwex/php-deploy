<?php
/**
 * User: aguidet
 * Date: 13/02/15
 * Time: 09:56
 */

namespace Deploy\Action;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionInit extends Command {

    protected function configure()
    {
        $this
            ->setName('config:init')
            ->setDescription('Initialize a new project in the current directory')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The project name that will be used for archive and deployment directory'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $configurationPath = getcwd() . '/.php-deploy';
        $envPath = $configurationPath . '/environments';


        if (!is_dir($configurationPath)) {

            $output->writeln("<info>Creating directories</info>");

            exec(
                sprintf(
                    "mkdir -p %s",
                    $configurationPath
                )
            );

            exec(
                sprintf(
                    "mkdir -p %s",
                    $configurationPath . '/Command'
                )
            );

            exec(
                sprintf(
                    "mkdir -p %s",
                    $envPath
                )
            );

            $output->writeln("<info>Creating initial configuration</info>");

            $configContent = file_get_contents(__DIR__ . '/../../templates/config.ini');
            $configContent = str_replace('{PROJECT_NAME}', $input->getArgument('name'), $configContent);
            touch($configurationPath . '/config.ini');
            file_put_contents($configurationPath . '/config.ini', $configContent);

            exec(
                sprintf(
                    "cp %s %s",
                    __DIR__ . '/../../templates/ExampleCommand.php',
                    $configurationPath . '/Command/'
                )
            );

            $output->writeln("<info>Project correctly initialized</info>");
            $output->writeln("<info>Please edit .php-deploy/config.ini and add environments to be ready to go</info>");

        } else {

            $output->writeln("<error>Project has already been initialized !!!</error>");

        }

    }

}