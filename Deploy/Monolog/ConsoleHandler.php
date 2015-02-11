<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:28
 */

namespace Deploy\Monolog;

class ConsoleHandler extends \Monolog\Handler\AbstractProcessingHandler {

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        echo ($record['level']['level_name'] . " " . (string) $record['formatted']);
    }
}