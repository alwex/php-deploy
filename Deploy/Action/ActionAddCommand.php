<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 03/03/15
 * Time: 18:46
 */

namespace Deploy\Action;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActionAddCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('command:add')
            ->setDescription('Create the an empty command in .php-deploy/Command/{name}.php')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The new command to create'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandName = ucfirst($input->getArgument('name'));

        $commandFile = getcwd() . '/.php-deploy/Command/' . $commandName . 'Command.php';
        if (file_exists($commandFile)) {
            throw new \RuntimeException("command [$commandName] has already been created");
        }

        $content = file_get_contents(__DIR__ . '/../../templates/TemplateCommand.tpl');

        file_put_contents($commandFile, str_replace('{COMMAND_NAME}', $commandName, $content));

        $output->writeln("<info>Command $commandName correctly created</info>");
        $output->writeln("<info>You can edit in $commandFile</info>");
    }
}