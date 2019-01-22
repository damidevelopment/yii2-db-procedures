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
    private $_result;


    /**
     * @inheritdoc
     */
    public function call()
    {
        if ($this->_result === null) {
            $this->_result = $this->execute('queryOne');
        }
        return $this->_result;
    }
}