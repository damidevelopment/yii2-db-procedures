<?php
/**
 * Created by PhpStorm.
 * User: stepanek
 * Date: 1/23/19
 * Time: 10:50 AM
 */

namespace damidev\dbprocedures\models\executors;

use yii\base\Model;
use yii\db\Connection;
use damidev\dbprocedures\models\database\IDatabaseAccessable;
use damidev\dbprocedures\models\database\TDatabaseAccess;


class ProcedureExecutor extends Model implements IExecutor, IDatabaseAccessable
{
    use TDatabaseAccess;

    /**
     * @param string $cmd
     * @param array $params
     * @param string $method
     * @return mixed
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