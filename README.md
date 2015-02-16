# php-deploy
php-deploy is a full php deployment tool designed to allow very common OR very specific tasks. It's main philosophy is 
to chain commands as described in a simple configuration file, commands are easily extensible and highly customizable.

## Installation using composer
If you use composer you can easily use it on your project. The main binary file will be available on your binary path
`bin/pdeploy` and ready to use.

`composer require alwex/pdeploy`

## Initiating the project
To use php-deploy you have to initialize your project. Some directory and basic configuration files will be created under the 
`.php-deploy` directory located in the root of your project.

`bin/pdeploy action:init myproject`

* _.php-deploy/config.ini_ -> the main configuration file which describe the global project configuration
* _.php-deploy/environments_ -> environment configuration files goes here
* _.php-deploy/Command_ -> you put your custom commands here

You may want to modify the config.ini file

## Adding some environments
In order to add environments simply type

`bin/pdeploy action:addenv production`

Environment file example:

```ini
# user used for all remote commands
login=mySshLogin

# directory where the project is located
# relatively to the path where you execute
# bin/pdeploy
fromDirectory = ./
# remote or local path where to deploy
# the application
toDirectory = /var/www
# list of hosts where to deploy the app
hosts[] = 'localhost'
hosts[] = 'web1'
hosts[] = 'web2'
# ...
# name of the symlink allowing
# multiple apps in the same path
symlink = current-php-deploy

[deploy]
# PRE TASK
# executed before task
# usually vcs export and build
# of the package to deploy
preTask[] = Deploy\Command\GitExport
preTask[] = Deploy\Command\ComposerInstall
preTask[] = Deploy\Command\TarGz
# deployment stage executed against
# each hosts
onTask[] = Deploy\Command\Scp
onTask[] = Deploy\Command\UnTarGz
# post deployment stage executed
# on each hosts, usually cache clear,
# apache reload and symlink generation
# after this stage, deployment is done!
postTask[] = Deploy\Command\Symlink
# after tasks executed one time only
afterTask[] = ExampleCommand # custom comand

[mytask]
# PRE TASK
preTask[] = Deploy\Command\Symlink
# ON TASK
onTask[] = Deploy\Command\Symlink
# POST TASK
postTask[] = Deploy\Command\Symlink
# AFTER TASK
afterTask[] = Deploy\Command\Symlink

# ... and so on as many tasks as necessary
```

## Executing a task

Once you have defined the tasks on the environment ini files, you can simply run them with

`bin/pdeploy action:execute --release=0.0.1 --env=production mytask`

## Creating custom commands

To add custom commands, simply add it as php classes on the folder `.php-deploy/Command`, you can duplicate the `ExampleCommand.php` to start.
Just add this custom command to a task like for example

`afterTask[] = ExampleCommand`

ExampleCommand content:

```php
class ExampleCommand extends \Deploy\Command\AbstractCommand {

    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        $command = "echo hello > /tmp/hello.txt";
        $this->shellExec($command);
    }

    /**
     * optionally you may check if the command has been
     * correctly done
     *
     * @throw \RuntimeException
     */
    public function check() {

        $expectedValue = 'hello';
        $fileContent = file_get_contents("/tmp/hello.txt");

        if ($fileContent != $expectedValue) {
            throw new \RuntimeException("hello file does not contain expected value '$expectedValue', found '$fileContent'");
        }
    }
}
```