<?php

namespace damidevelopment\dbprocedures\models\database;

use yii\db\Connection;

/**
 * Trait TDatabaseAccess
 * @author Martin Štěpánek
 * @date 1/23/19 11:51 AM
 * @package damidevelopment\dbprocedures\models\database
 */
trait TDatabaseAccess
{

    /**
     * @var Connection Yii database connection
     */
    private $_db;

    /**
     * Sets Yii database connection
     * @param Connection Yii database connection
     * @return self
     */
    public function setDb(Connection $db): self
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Returns configured database connection.
     * If database connection is to configured, returns default db component of Yii::$app.
     * @return Connection Yii database connection
     */
    public function getDb(): Connection
    {
        if ($this->_db === null) {
            $this->_db = Yii::$app->getDb();
        }
        return $this->_db;
    }

}