<?php
/**
 * User: aguidet
 * Date: 06/03/15
 * Time: 16:38
 */

exec('wget --output-document=/tmp/php-deploy.zip https://github.com/alwex/php-deploy/archive/master.zip');
exec('unzip -d /etc/ /tmp/php-deploy.zip');
exec('mv /etc/php-deploy-master /etc/php-deploy');
exec('cd /etc/php-deploy/ && composer install --optimize-autoloader');
exec('cd /etc/php-deploy/ && chmod +x bin/pdeploy');
exec('cd /etc/php-deploy/ && bin/pdeploy config:init global');