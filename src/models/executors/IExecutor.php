<?php
/**
 * Created by PhpStorm.
 * User: stepanek
 * Date: 1/23/19
 * Time: 10:51 AM
 */

namespace damidev\dbprocedures\models\executors;


interface IExecutor
{

    /**
     * @param string $cmd
     * @param array $params
     * @param string $method
     * @return mixed
     */
    public function execute(string $cmd, array $params, string $method);

}