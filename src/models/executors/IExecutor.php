<?php

namespace damidevelopment\dbprocedures\models\executors;

/**
 * Interface IExecutor
 * @author Martin Štěpánek
 * @date 1/23/19 10:51 AM
 * @package damidevelopment\dbprocedures\models\executors
 */
interface IExecutor
{

    /**
     * Executes command
     * @param string $cmd Command to execute
     * @param array $params Params to command
     * @param string $method Method that should be executed on command (queryOne, queryAll, etc.)
     * @return mixed Data payload
     */
    public function execute(string $cmd, array $params, string $method);

}