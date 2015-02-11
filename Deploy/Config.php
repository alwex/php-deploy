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

    public function __construct(Arguments $arguments) {

        $env = $arguments->getTo();

        $this->configurationPath = getcwd() . '/.php-deploy';
        $this->envDirectory = $this->configurationPath . '/environments/';

        @$envConfig = parse_ini_file($this->envDirectory . $env . '.ini');
        @$globalConfig = parse_ini_file($this->configurationPath . '/config.ini');

        // loaf global config
        if ($globalConfig) {
            $this->setProject(ArrayUtil::getArrayValue($globalConfig, 'project'));
        }

        // load env specific config
        if ($envConfig) {
            $this->setLogin(ArrayUtil::getArrayValue($envConfig, 'login'));
            $this->setFromDirectory(ArrayUtil::getArrayValue($envConfig, 'fromDirectory'));
            $this->setToDirectory(ArrayUtil::getArrayValue($envConfig, 'toDirectory'));
            $this->setSymlink(ArrayUtil::getArrayValue($envConfig, 'symlink'));
            $this->setHosts(ArrayUtil::getArrayValue($envConfig, 'hosts'));
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
                    $this->envDirectory
                )
            );

            touch($this->envDirectory . '/template/example.ini');
            $exampleFile = <<<EXAMPLE
[user]
login=sshuser

[deployment]
fromDirectory = ./
toDirectory = /tmp/monrep

hosts[] = 'localhost'
hosts[] = 'localhost'
hosts[] = 'localhost'
hosts[] = 'localhost'

symlink = current
EXAMPLE;

            file_put_contents($this->envDirectory . '/template/example.ini', $exampleFile);

            touch($this->configurationPath);
            $globalConfiguration =<<<GLOBAL_CONF
project = $project
vcs=http://localhost/project.git
GLOBAL_CONF;
            file_put_contents($this->configurationPath .'/config.ini', $globalConfiguration);

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