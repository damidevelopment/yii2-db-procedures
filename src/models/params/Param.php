<?php


namespace damidevelopment\dbprocedures\models\params;


use yii\base\BaseObject;
use yii\db\Command;

abstract class Param extends BaseObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * @param Command $command
     * @return mixed
     */
    public abstract function bindParam($command);

    public function formatParam()
    {
        return '@' . $this->name . ' = :' . $this->name;
    }}