<?php

namespace damidevelopment\dbprocedures\models\database;

use yii\db\Connection;

/**
 * Interface that should be used with TDatabaseAccess trait
 *
 * Interface IDatabaseAccessable
 * @author Martin Štěpánek
 * @date 1/23/19 11:51 AM
 * @package damidevelopment\dbprocedures\models\database
 */
interface IDatabaseAccessable
{

    /**
     * Sets Yii database connection
     * @param Connection $db Yii database connection
     * @return IDatabaseAccessable
     */
    public function setDb(Connection $db): self;

    /**
     * Returns Yii database connection
     * @return Connection Yii database connection
     */
    public function getDb(): Connection;

}