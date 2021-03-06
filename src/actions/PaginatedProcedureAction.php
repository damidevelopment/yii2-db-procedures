<?php

namespace damidevelopment\dbprocedures\actions;

use damidevelopment\dbprocedures\models\PaginatedProcedure;
use damidevelopment\dbprocedures\models\ProcedureDataProvider;
use Yii;


/**
 * PaginatedProcedureAction provides easy way to call procedure with input (GET or POST)
 * and return ProcedureDataProvider containing data from procedure.
 *
 * Class PaginatedProcedureAction
 * @author: Jakub Hrášek
 * @date:   2018-07-02 07:41:30
 * @package damidevelopment\dbprocedures\actions
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
     * In case of error, returns procedure with errors,
     * otherwise returns ProcedureDataProvider containing data
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