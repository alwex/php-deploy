<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 16:38
 */

exec('wget --output-document=/tmp/php-deploy.zip https://github.com/alwex/php-deploy/archive/master.zip');
exec('unzip -d /usr/share/php/ /tmp/php-deploy.zip');
exec('mv /usr/share/php/php-deploy-master /usr/share/php/php-deploy');
exec('cd /usr/share/php/php-deploy/ && composer install --optimize-autoloader');
exec('cd /usr/share/php/php-deploy/ && chmod +x bin/pdeploy');
exec('cd /usr/share/php/php-deploy/ && bin/pdeploy config:init global');
$launcher =<<<LAUNCHER
#/bin/bash
php /usr/share/php/php-deploy/bin/pdeploy $@
LAUNCHER;

file_put_contents('/usr/local/bin/pdeploy', $launcher);

exec('chmod +x /usr/local/bin/pdeploy');

