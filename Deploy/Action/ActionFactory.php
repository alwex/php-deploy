<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:17
 */

namespace Deploy\Action;

use Deploy\Config;
use Deploy\Arguments;
use Monolog\Logger;
use Deploy\Command;

class ActionFactory {

    /**
     * @param $actionName
     * @param Logger $logger
     * @return Deploy|Init|Rollback|null
     * @throw \RuntimeException
     */
    public static function create($actionName, Arguments $arguments, Config $config, Logger $logger) {

        $action = null;

        switch($actionName) {
            case Arguments::ACTION_INIT:
                $action = new Init($arguments, $config, $logger);
                break;

            case Arguments::ACTION_DEPLOY:
                $action = new Deploy($arguments, $config, $logger);
                break;

            case Arguments::ACTION_ROLLBACK:
                $action = new Rollback($arguments, $config, $logger);
                break;

            default:
                throw new \RuntimeException('not action found for ' . $actionName, 10);
        }

        return $action;
    }

}