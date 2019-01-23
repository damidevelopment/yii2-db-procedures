<?php

namespace damidev\dbprocedures\actions;

use damidev\dbprocedures\models\PaginatedProcedure;
use damidev\dbprocedures\models\ProcedureDataProvider;
use Yii;


/**
 * Paginated procedure action provides easy way to call procedure with input (GET or POST)
 * and return ProcedureDataProvider containing data from procedure.
 *
 * @Author: Jakub Hrášek
 * @Date:   2018-07-02 07:41:30
 */
class PaginatedProcedureAction extends ProcedureAction
{

    /**
     * @var array|callable Filter that is applied after call.
     * The anonymous function signature should be: `function(array $input): array`.
     */
    public $filter = [];

    /**
     * @var PaginatedProcedure Paginated procedure that will be called.
     * Procedure must implement app\\models\\PaginatedProcedure
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
     * In case of error, is returned procedure with errors,
     * otherwise is returned ProcedureDataProvider containing data
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