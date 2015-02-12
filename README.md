# php-deploy
php-deploy is a full php deployment tool designed to allow very common OR very specific tasks. It's main philosophy is 
to chain commands as described in a simple configuration file, commands are easily extensible and highly customizable.

## Installation using composer
If you use composer you can easily use it on your project. The main binary file will be available on your binary path
`bin/pdeploy` and ready to use.

```javascript
"require-dev": {
    "alwex/phpdeploy": "1.0.*"
}
```

then update your dependencies

`composer update alwex/pdeploy`

## Initiating the project
To use php-deploy you have to initialize your project. Some directory and basic configuration files will be created under the 
`.php-deploy` directory located in the root of your project.

`bin/pdeploy --init --project=myapp --to=dev`

* _.php-deploy/config.ini_ -> the main configuration file which describe the global project configuration
* _.php-deploy/environments_ -> environment configuration files goes here
* _.php-deploy/environments/dev.ini_ -> environment configuration files for the dev environment
* _.php-deploy/environments/template_ -> environment configuration template is here
* _.php-deploy/Command_ -> you put your custom commands here

In order to add other environment simply type again the init command changing the `to` parameter

eg:

`bin/pdeploy --init --project=myapp --to=prod`

here is the _config.ini_ file

```ini
# project name used to create
# archives/package files
project = php-deploy
# url of your vcs if needed
vcs=https://github.com/alwex/php-deploy.git
# working directory where php-deploy
# will make is export/clone stuff
workingDirectory=/tmp/php-deploy
```

And the dev.ini file

```ini
[user]
# user used for all remote commands
login=aguidet

[deployment]
# directory where the project is located
# relatively to the path where you execute
# bin/pdeploy
fromDirectory = ./
# remote or local path where to deploy
# the application
toDirectory = /var/wwww/myapp
# list of hosts where to deploy the app
hosts[] = 'localhost'
hosts[] = 'localhost'
# name of the symlink allowing
# multiple apps in the same path
symlink = current

[command]
# PRE DEPLOY
# executed before deployment
# usually vcs export and build
# of the package to deploy
preDeploy[] = Deploy\Command\GitExport
preDeploy[] = Deploy\Command\ComposerInstall
preDeploy[] = Deploy\Command\TarGz
# deployment stage executed against
# each hosts
onDeploy[] = Deploy\Command\Scp
onDeploy[] = Deploy\Command\UnTarGz
# post deployment stage executed
# on each hosts, usually cache clear,
# apache reload and symlink generation
# after this stage, deployment is done!
postDeploy[] = Deploy\Command\Symlink
# custom commands located in .php-deploy/Command path
# postDeploy[] = Ls
# postDeploy[] = EchoCmd
```