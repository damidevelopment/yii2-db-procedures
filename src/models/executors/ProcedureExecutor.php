<?php

namespace damidevelopment\dbprocedures\models\executors;

use yii\base\Model;
use yii\db\Connection;
use damidevelopment\dbprocedures\models\database\IDatabaseAccessable;
use damidevelopment\dbprocedures\models\database\TDatabaseAccess;

/**
 * Class ProcedureExecutor
 * @author Martin Štěpánek
 * @date 1/23/19 10:50 PM
 * @package damidev\dbprocedures\models\executors
 */
class ProcedureExecutor extends Model implements IExecutor, IDatabaseAccessable
{
    use TDatabaseAccess;

    /**
     * Executes command on configured database
     * @param string $cmd Command to execute
     * @param array $params Params to command
     * @param string $method Method that should be executed on command (queryOne, queryAll, etc.)
     * @return mixed Data payload
     */
    public function execute(string $cmd, array $params, string $method)
    {
        $command = $this->getDb()->createCommand($cmd);

        // bind params
        foreach ($params as $attr => $value) {
            $command->bindValue(':' . $attr, $value);
        }
        $result = call_user_func([$command, $method]);
        return $result;
    }

}