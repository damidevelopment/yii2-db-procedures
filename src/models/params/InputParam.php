<?php


namespace damidevelopment\dbprocedures\models\params;


use yii\db\Command;

class InputParam extends Param
{
    /**
     * @param Command $command
     * @return mixed|void
     */
    public function bindParam($command)
    {
        $command->bindValue(':' . $this->name, $this->value);
    }
}