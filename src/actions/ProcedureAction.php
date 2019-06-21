<?php

namespace damidev\dbprocedures\actions;


use damidevelopment\dbprocedures\models\IProcedure;
use Yii;
use yii\base\Action;

/**
 * ProcedureAction is base abstract class for every procedure action providing easy configuration.
 *
 * Class ProcedureAction
 * @author: Martin Štěpánek
 * @date: 03/07/2018 12:56
 * @package damidev\dbprocedures\actions
 */
abstract class ProcedureAction extends Action
{

    /**
     * String representing procedure class name. Instantiated object must implement IProcedure.
     *
     * @var string
     */
    public $procedureClass;

    /**
     * String representing resource class name. Must be instance of damidev\\dbprocedures\\models\\Resource
     *
     * @var string
     */
    public $resourceClass;

    /**
     * Callable object.
     * The anonymous function signature should be: `function(): array`.
     * These data will be used to load input parameters for procedure.
     *
     * @var array|\Closure
     */
    public $input;

    /**
     * @var IProcedure Instance of IProcedure created base on ProcedureAction::procedureClass
     */
    protected $procedure;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $class = $this->procedureClass;
        $this->procedure = new $class;

        if (!$this->procedure instanceof IProcedure) {
            throw new \InvalidArgumentException('Procedure must implement app\\models\\IProcedure');
        }
    }

    /**
     * Returns input for procedure.
     * If is input configured (array or closure) returns this configuration,
     * otherwise returns input based on request method (GET or POST).
     * @return array Input for procedure
     */
    protected function getInput(): array
    {
        $request = Yii::$app->getRequest();
        if ($this->input) {
            $input = call_user_func($this->input);
            $input['page'] = $request->get('page');
            return $input;
        }

        switch ($request->getMethod()) {
            case 'POST':
                return $request->post();
            case 'GET':
                return $request->get();
            default:
                return [];
        }
    }
}