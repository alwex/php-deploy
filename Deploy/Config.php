<?php
/**
 * User: aguidet
 * Date: 09/02/15
 * Time: 17:32
 */

namespace Deploy;

use Deploy\Util\ArrayUtil;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Config {

    private $configurationPath;
    private $hosts = array();
    private $fromDirectory;
    private $toDirectory;
    private $envDirectory;
    private $login;
    private $symlink;
    private $project;
    private $currentHost;
    private $vcs;
    private $workingDirectory;
    private $preDeployCommands;
    private $onDeployCommands;
    private $postDeployCommands;

    public function __construct(Arguments $arguments) {

        $env = $arguments->getTo();

        $this->configurationPath = getcwd() . '/.php-deploy';
        $this->envDirectory = $this->configurationPath . '/environments/';

        @$envConfig = parse_ini_file($this->envDirectory . $env . '.ini');
        @$globalConfig = parse_ini_file($this->configurationPath . '/config.ini');

        // loaf global config
        if ($globalConfig) {
            $this->setProject(ArrayUtil::getArrayValue($globalConfig, 'project'));
            $this->setVcs(ArrayUtil::getArrayValue($globalConfig, 'vcs'));
            $this->setWorkingDirectory(ArrayUtil::getArrayValue($globalConfig, 'workingDirectory'));
        }

        // load env specific config
        if ($envConfig) {
            $this->setLogin(ArrayUtil::getArrayValue($envConfig, 'login'));
            $this->setFromDirectory(ArrayUtil::getArrayValue($envConfig, 'fromDirectory'));
            $this->setToDirectory(ArrayUtil::getArrayValue($envConfig, 'toDirectory'));
            $this->setSymlink(ArrayUtil::getArrayValue($envConfig, 'symlink'));
            $this->setHosts(ArrayUtil::getArrayValue($envConfig, 'hosts'));

            $this->setPreDeployCommands(ArrayUtil::getArrayValue($envConfig, 'preDeploy'));
            $this->setOnDeployCommands(ArrayUtil::getArrayValue($envConfig, 'onDeploy'));
            $this->setPostDeployCommands(ArrayUtil::getArrayValue($envConfig, 'postDeploy'));
        }
    }

    /**
     * @param $env
     */
    public function init(Arguments $arguments) {

        $env = $arguments->getTo();
        $project = $arguments->getProject();

        if (!is_dir($this->configurationPath)) {
            exec(
                sprintf(
                    "mkdir -p %s",
                    $this->configurationPath
                )
            );

            exec(
                sprintf(
                    "mkdir -p %s",
                    $this->configurationPath . '/Command'
                )
            );

            exec(
                sprintf(
                    "mkdir -p %s",
                    $this->envDirectory
                )
            );

            exec(
                sprintf(
                    "mkdir -p %s",
                    $this->envDirectory . '/template'
                )
            );

            touch($this->envDirectory . '/template/example.ini');
            $exampleFile = <<<EXAMPLE
[user]
login=aguidet

[deployment]
fromDirectory = ./
toDirectory = /tmp/monrep

hosts[] = 'localhost'
hosts[] = 'localhost'

symlink = current

[command]
preDeploy[] = Deploy\Command\GitExport
preDeploy[] = Deploy\Command\ComposerInstall
preDeploy[] = Deploy\Command\TarGz

onDeploy[] = Deploy\Command\Scp
onDeploy[] = Deploy\Command\UnTarGz

postDeploy[] = Deploy\Command\Symlink
EXAMPLE;

            file_put_contents($this->envDirectory . '/template/example.ini', $exampleFile);

            touch($this->configurationPath);
            $globalConfiguration =<<<GLOBAL_CONF
project = $project
vcs=https://github.com/$project.git
workingDirectory=/tmp/php-deploy
GLOBAL_CONF;
            file_put_contents($this->configurationPath . '/config.ini', $globalConfiguration);


            $exampleCommand =<<<'EXAMPLE_COMMAND'
<?php

class Ls extends \Deploy\Command\AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $command = sprintf(
            "cd %s && ls -l",
            $this->config->getFromDirectory()
        );

        exec($command, $this->output);
    }
}
EXAMPLE_COMMAND;

            touch($this->configurationPath . '/Command/Ls.php');
            file_put_contents($this->configurationPath . '/Command/Ls.php', $exampleCommand);
        }


        if (!file_exists($this->envDirectory . $env . '.ini')) {
            // generate the default env configuration file
            touch($this->envDirectory . $env . '.ini');
            file_put_contents(
                $this->envDirectory . $env . '.ini',
                file_get_contents($this->envDirectory . 'template/example.ini')
            );
        } else {
            throw new RuntimeException(
                sprintf("configuration file for environment %s already exists", $env),
                2
            );
        }
    }

    /**
     * @return mixed
     */
    public function getPreDeployCommands()
    {
        return $this->preDeployCommands;
    }

    /**
     * @param mixed $preDeployCommands
     */
    public function setPreDeployCommands($preDeployCommands)
    {
        $this->preDeployCommands = $preDeployCommands;
    }

    /**
     * @return mixed
     */
    public function getOnDeployCommands()
    {
        return $this->onDeployCommands;
    }

    /**
     * @param mixed $onDeployCommands
     */
    public function setOnDeployCommands($onDeployCommands)
    {
        $this->onDeployCommands = $onDeployCommands;
    }

    /**
     * @return mixed
     */
    public function getPostDeployCommands()
    {
        return $this->postDeployCommands;
    }

    /**
     * @param mixed $postDeployCommands
     */
    public function setPostDeployCommands($postDeployCommands)
    {
        $this->postDeployCommands = $postDeployCommands;
    }

    /**
     * @return mixed
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * @param mixed $workingDirectory
     */
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @return mixed
     */
    public function getVcs()
    {
        return $this->vcs;
    }

    /**
     * @param mixed $vcs
     */
    public function setVcs($vcs)
    {
        $this->vcs = $vcs;
    }

    /**
     * @return string
     */
    public function getConfigurationPath()
    {
        return $this->configurationPath;
    }

    /**
     * @param string $configurationPath
     */
    public function setConfigurationPath($configurationPath)
    {
        $this->configurationPath = $configurationPath;
    }

    /**
     * @return mixed
     */
    public function getCurrentHost()
    {
        return $this->currentHost;
    }

    /**
     * @param mixed $currentHost
     */
    public function setCurrentHost($currentHost)
    {
        $this->currentHost = $currentHost;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @param array $hosts
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
    }

    /**
     * @return mixed
     */
    public function getFromDirectory()
    {
        return $this->fromDirectory;
    }

    /**
     * @param mixed $fromDirectory
     */
    public function setFromDirectory($fromDirectory)
    {
        $this->fromDirectory = $fromDirectory;
    }

    /**
     * @return mixed
     */
    public function getToDirectory()
    {
        return $this->toDirectory;
    }

    /**
     * @param mixed $toDirectory
     */
    public function setToDirectory($toDirectory)
    {
        $this->toDirectory = $toDirectory;
    }

    /**
     * @return string
     */
    public function getEnvDirectory()
    {
        return $this->envDirectory;
    }

    /**
     * @param string $envDirectory
     */
    public function setEnvDirectory($envDirectory)
    {
        $this->envDirectory = $envDirectory;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getSymlink()
    {
        return $this->symlink;
    }

    /**
     * @param mixed $symlink
     */
    public function setSymlink($symlink)
    {
        $this->symlink = $symlink;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

}