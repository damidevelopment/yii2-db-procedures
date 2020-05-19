<?php

namespace damidevelopment\dbprocedures\models;

/**
 * Class SimpleProcedure
 * @author Martin Štěpánek
 * @date 7/3/18 13:13 PM
 * @package damidevelopment\dbprocedures\models
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
            $data = $this->execute('queryAll');

            if (count($data) > 0) {
                $this->_data = $data[0];
            }
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