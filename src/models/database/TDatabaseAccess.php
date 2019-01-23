<?php
/**
 * Created by PhpStorm.
 * User: stepanek
 * Date: 1/23/19
 * Time: 11:05 AM
 */

namespace damidev\dbprocedures\models\database;

use yii\db\Connection;

trait TDatabaseAccess
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

}