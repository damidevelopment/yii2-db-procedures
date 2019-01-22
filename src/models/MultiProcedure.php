<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 11/07/2018 11:52
 */

namespace damidev\dbprocedures\models;


class MultiProcedure extends Procedure
{

    private $_result;

    public function call()
    {
        if ($this->_result === null) {
            $this->_result = $this->execute('queryAll');
        }
        return $this->_result;
    }

}