<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 13:13
 */

namespace damidev\dbprocedures\models;


class SimpleProcedure extends Procedure
{
    /**
     * @var mixed
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
     * Refresh procedure data
     */
    public function refresh(): void
    {
        $this->_data = null;
    }
}