<?php


namespace damidevelopment\dbprocedures\models\params;


use yii\db\mssql\PDO;

class InputOutputParam extends Param
{
    /**
     * PDO type of param, could be PDO::PARAM_STR
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $length;

    public function bindParam($command)
    {
        $command->bindParam($this->name, $this->value, $this->type | PDO::PARAM_INPUT_OUTPUT, $this->length);
    }
}