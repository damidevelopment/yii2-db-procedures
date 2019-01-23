<?php
/**
 * Created by PhpStorm.
 * User: stepanek
 * Date: 1/23/19
 * Time: 10:50 AM
 */

namespace damidev\dbprocedures\models;

use yii\base\Model;
use yii\db\Connection;


class ProcedureExecutor extends Model implements IExecutor
{

    /**
     * @var Connection
     */
    private $_db;

    /**
     * @param Connection
     * @return self
     */
    public function setDb(Connection $db): self
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * @return Connection
     */
    public function getDb(): Connection
    {
        if ($this->_db === null) {
            $this->_db = Yii::$app->getDb();
        }
        return $this->_db;
    }

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