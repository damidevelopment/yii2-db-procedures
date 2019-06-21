<?php

namespace damidevelopment\dbprocedures\models;

/**
 * Class SimpleProcedure
 * @author Martin Štěpánek
 * @date 7/3/18 13:13 PM
 * @package damidev\dbprocedures\models
 */
class SimpleProcedure extends Procedure
{
    /**
     * @var mixed Data payload from procedure
     */
    private $_data;


    /**
     * @inheritdoc
     */
    public function call()
    {
        if ($this->_data === null) {
            $this->_data = $this->execute('queryOne');
        }
        return $this->_data;
    }

    /**
     * Refreshes procedure data
     */
    public function refresh(): void
    {
        $this->_data = null;
    }
}