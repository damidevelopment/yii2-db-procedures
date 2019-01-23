<?php
/**
 * Created by PhpStorm.
 * User: stepanek
 * Date: 1/23/19
 * Time: 11:06 AM
 */

namespace damidev\dbprocedures\models\database;

use yii\db\Connection;

interface IDatabaseAccessable
{

    /**
     * @param Connection $db
     * @return IDatabaseAccessable
     */
    public function setDb(Connection $db): self;

    /**
     * @return Connection
     */
    public function getDb(): Connection;

}