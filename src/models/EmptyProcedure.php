<?php

namespace damidevelopment\dbprocedures\models;

/**
 * Class EmptyProcedure
 * @author Martin Štěpánek
 * @date 7/3/18 13:13 PM
 * @package damidevelopment\dbprocedures\models
 */
class EmptyProcedure extends Procedure
{
    /**
     * @inheritdoc
     */
    public function call()
    {
        return $this->execute('execute');
    }
}