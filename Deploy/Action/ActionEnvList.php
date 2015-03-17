<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 09:53
 */

namespace Deploy\Action;


use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActionEnvList extends AbstractAction
{
    protected function configure()
    {
        $this
            ->setName('env:list')
            ->setDescription('List the available env')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $envPath = getcwd() . '/.php-deploy/environments';
        $iterator = new \IteratorIterator(
            new \RecursiveDirectoryIterator($envPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $output->writeln('<comment>Available env</comment>');
        /* @var $object \SplFileInfo */
        foreach ($iterator as $file => $object) {
            $output->writeln('<info>' . str_replace('.yml', '', $object->getFileName()) . '</info>');
        }

        $output->writeln('<comment>To list available task for env please type</comment>');
        $output->writeln('phpdeploy task:list --env=[env] [-v]');
    }
}