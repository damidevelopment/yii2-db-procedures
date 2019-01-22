<?php

namespace damidev\dbprocedures\actions;

use damidev\dbprocedures\models\PaginatedProcedure;
use damidev\dbprocedures\models\ProcedureDataProvider;
use Yii;


/**
 * @Author: Jakub Hrášek
 * @Date:   2018-07-02 07:41:30
 */
class PaginatedProcedureAction extends ProcedureAction
{

    /**
     * @var array|callable Filter that is applied after call
     */
    public $filter = [];

    /**
     * @var PaginatedProcedure
     */
    protected $procedure;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->procedure instanceof PaginatedProcedure) {
            throw new \InvalidArgumentException('Procedure must implement app\\models\\PaginatedProcedure');
        }
    }

    /**
     * @inheritdoc
     *
     * @return PaginatedProcedure|ProcedureDataProvider
     */
    public function run()
    {
        $input = $this->getInput();

        if(is_callable($this->filter)){
            $this->filter = call_user_func($this->filter, $input);
        }

        $this->procedure->load($input, '');
        $this->procedure->filter = $this->filter;

        if (!$this->procedure->validate()) {
            return $this->procedure;
        }

        return Yii::createObject([
            'class' => ProcedureDataProvider::class,
            'procedure' => $this->procedure,
            'resourceClass' => $this->resourceClass,
        ]);
    }
}