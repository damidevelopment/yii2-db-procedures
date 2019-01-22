<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 12:56
 */

namespace damidev\dbprocedures\actions;


use app\models\ProcedureInterface;
use Yii;
use yii\base\Action;

abstract class ProcedureAction extends Action
{

    /**
     * String representing procedure class name. Instantiated object must implement PaginatedProcedure.
     *
     * @var string
     */
    public $procedureClass;

    /**
     * String representing resource class name.
     *
     * @var string
     */
    public $resourceClass;

    /**
     * Callable object. Must return array. These data will be used to load input parameters for procedure.
     *
     * @var array|Closure
     */
    public $input;

    /**
     * @var ProcedureInterface
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

        if (!$this->procedure instanceof ProcedureInterface) {
            throw new \InvalidArgumentException('Procedure must implement app\\models\\ProcedureInterface');
        }
    }

    /**
     * @return array
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