<?php
/**
 * User: aguidet
 * Date: 09/02/15
 * Time: 17:09
 */

namespace Deploy;

use Deploy\Action\ActionFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Deploy\Util\ArrayUtil;
use Deploy\Monolog\ConsoleHandler;

class Console {

    private $actions = array(
        Arguments::ACTION_INIT,
        Arguments::ACTION_DEPLOY,
        Arguments::ACTION_ROLLBACK,
    );

    private $params = array(
        'action:',
        'to:',
        'release:',
        'project:',
    );

    private $longOptions;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Arguments
     */
    private $arguments;

    public function __construct() {
        // log configuration
        $this->logger = new Logger("php-deploy");
        $this->logger->pushHandler(
            new StreamHandler('/tmp/deploy.log')
        );

        $this->logger->pushHandler(
            new ConsoleHandler()
        );

        // generate option parser
        $this->longOptions = array_merge(
            $this->actions,
            $this->params
        );
    }

    public function run() {
        // parse args
        $this->parseArgs();

        // success by default
        $returnCode = 0;
        try {
            $action = ActionFactory::create(
                $this->arguments->getAction(),
                $this->arguments,
                $this->config,
                $this->logger
            );

            $action->process();

        } catch(\RuntimeException $e) {
            $returnCode = $e->getCode();
            $this->logger->addError($e->getMessage());
        }

        exit($returnCode);
    }

    public function parseArgs() {
        // long options only
        $options = getopt('', $this->longOptions);


        $arguments = new Arguments();

        // find the action
        foreach($options as $key => $value) {
            if (in_array($key, $this->actions)) {
                $arguments->setAction($key);
            }
        }

        // deal with params
        $arguments->setTo(ArrayUtil::getArrayValue($options, Arguments::PARAM_TO));
        $arguments->setRelease(ArrayUtil::getArrayValue($options, Arguments::PARAM_RELEASE));
        $arguments->setProject(ArrayUtil::getArrayValue($options, Arguments::PARAM_PROJECT));

        $config = new Config($arguments);

        $this->arguments = $arguments;
        $this->config = $config;

        $this->logger->debug(json_encode($options));
    }
}