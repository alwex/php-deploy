<?php
/**
 * User: aguidet
 * Date: 09/02/15
 * Time: 17:32
 */

namespace Deploy;

use Deploy\Util\ArrayUtil;
use Symfony\Component\Console\Input\InputInterface;

class Config
{

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
    private $preTaskCommands;
    private $onTaskCommands;
    private $postTaskCommands;
    private $afterTaskCommands;
    private $packageName;
    private $rawConfiguration;

    /**
     * @param InputInterface $input
     * @return Config
     */
    public static function load(InputInterface $input)
    {
        $env = $input->getOption('env');

        if (file_exists(getcwd() . '/.php-deploy')) {
            $configurationPath = getcwd() . '/.php-deploy';
        } else {
            $configurationPath = '/etc/php-deploy/';
        }

        $envConfig = self::loadEnv($env);
        @$globalConfig = parse_ini_file($configurationPath . '/config.ini');

        $configuration = new Config();

        $task = $input->getArgument('task');

        $rawConfiguration = array();
        // load global config
        if ($globalConfig) {
            $configuration->setProject(ArrayUtil::getArrayValue($globalConfig, 'project'));
            $configuration->setVcs(ArrayUtil::getArrayValue($globalConfig, 'vcs'));
            $configuration->setWorkingDirectory(ArrayUtil::getArrayValue($globalConfig, 'workingDirectory'));

            $rawConfiguration = array_merge($rawConfiguration, $globalConfig);
        }

        // load env specific config
        if ($envConfig) {

            if (!isset($envConfig[$task])) {
                throw new \RuntimeException("task $task is not defined for env $env");
            }

            $configuration->setLogin(ArrayUtil::getArrayValue($envConfig, 'login'));
            $configuration->setFromDirectory(ArrayUtil::getArrayValue($envConfig, 'fromDirectory'));
            $configuration->setToDirectory(ArrayUtil::getArrayValue($envConfig, 'toDirectory'));
            $configuration->setSymlink(ArrayUtil::getArrayValue($envConfig, 'symlink'));
            $configuration->setHosts(ArrayUtil::getArrayValue($envConfig, 'hosts', array()));

            $configuration->setPreTaskCommands(ArrayUtil::getArrayValue($envConfig[$task], 'preTask', array()));
            $configuration->setOnTaskCommands(ArrayUtil::getArrayValue($envConfig[$task], 'onTask', array()));
            $configuration->setPostTaskCommands(ArrayUtil::getArrayValue($envConfig[$task], 'postTask', array()));
            $configuration->setAfterTaskCommands(ArrayUtil::getArrayValue($envConfig[$task], 'afterTask', array()));

            $rawConfiguration = array_merge($rawConfiguration, $envConfig);
        }

        $configuration->setRawConfiguration($rawConfiguration);

        return $configuration;
    }


    public static function loadEnv($env = 'dev')
    {

        if (is_dir(getcwd() . '/.php-deploy')) {
            $configurationPath = getcwd() . '/.php-deploy';
        } else {
            $configurationPath = '/etc/php-deploy';
        }

        $envPath = $configurationPath . '/environments';

        if (!file_exists($envPath . '/' . $env . '.ini')) {

            throw new \RuntimeException("env " . $env . " is not defined");

        }

        @$envConfig = parse_ini_file($envPath . '/' . $env . '.ini', true);

        return $envConfig;
    }

    public function get($name)
    {
        return ArrayUtil::getArrayValue($this->getRawConfiguration(), $name, null);
    }

    /**
     * @return mixed
     */
    public function getRawConfiguration()
    {
        return $this->rawConfiguration;
    }

    /**
     * @param mixed $rawConfiguration
     */
    public function setRawConfiguration($rawConfiguration)
    {
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * @return mixed
     */
    public function getAfterTaskCommands()
    {
        return $this->afterTaskCommands;
    }

    /**
     * @param mixed $afterTaskCommands
     */
    public function setAfterTaskCommands($afterTaskCommands)
    {
        $this->afterTaskCommands = $afterTaskCommands;
    }

    /**
     * @return mixed
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * @param mixed $packageName
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * @return mixed
     */
    public function getPreTaskCommands()
    {
        return $this->preTaskCommands;
    }

    /**
     * @param mixed $preTaskCommands
     */
    public function setPreTaskCommands($preTaskCommands)
    {
        $this->preTaskCommands = $preTaskCommands;
    }

    /**
     * @return mixed
     */
    public function getOnTaskCommands()
    {
        return $this->onTaskCommands;
    }

    /**
     * @param mixed $onTaskCommands
     */
    public function setOnTaskCommands($onTaskCommands)
    {
        $this->onTaskCommands = $onTaskCommands;
    }

    /**
     * @return mixed
     */
    public function getPostTaskCommands()
    {
        return $this->postTaskCommands;
    }

    /**
     * @param mixed $postTaskCommands
     */
    public function setPostTaskCommands($postTaskCommands)
    {
        $this->postTaskCommands = $postTaskCommands;
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
        if ($login == '' || $login == null) {
            $this->login = $current_user = trim(shell_exec('whoami'));
        }
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