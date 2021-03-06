<?php

namespace damidevelopment\dbprocedures\models;

use damidevelopment\dbprocedures\models\database\IDatabaseAccessable;
use damidevelopment\dbprocedures\models\database\TDatabaseAccess;
use damidevelopment\dbprocedures\models\events\AfterCallEvent;
use damidevelopment\dbprocedures\models\executors\IExecutor;
use damidevelopment\dbprocedures\models\executors\ProcedureExecutor;
use damidevelopment\dbprocedures\models\params\InputOutputParam;
use damidevelopment\dbprocedures\models\params\InputParam;
use damidevelopment\dbprocedures\models\params\Param;
use Yii;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;


/**
 * Class Procedure
 * @author Jakub Hrášek
 * @date 2018-06-13 18:02:19
 * @package damidevelopment\dbprocedures\models
 */
abstract class Procedure extends Model implements IProcedure, IDatabaseAccessable
{
    use TDatabaseAccess;

    /**
     * @event Event an event that is triggered before Procedure::call()
     */
    const EVENT_BEFORE_CALL = 'beforeCall';

    /**
     * @event Event an event that is triggered after Procedure::call()
     */
    const EVENT_AFTER_CALL = 'afterCall';

    /**
     * Scenario that is set up when Procedure::call()
     */
    const SCENARIO_CALL = 'procedureCall';

    /**
     * @var IExecutor Executor that execute command on Procedure::call()
     */
    private $_executor;

    /**
     * @var Param[] Procedure parameters
     */
    private $_params;

    /**
     * Sets Executor
     * @param IExecutor $executor Executor that execute command on Procedure::call()
     * @return Procedure
     */
    public function setExecutor(IExecutor $executor): self
    {
        $this->_executor = $executor;
        return $this;
    }

    /**
     * Returns executor
     * @return IExecutor Executor that execute command on Procedure::call()
     */
    public function getExecutor(): IExecutor
    {
        if ($this->_executor === null) {
            $this->_executor = Yii::createObject([
                'class' => ProcedureExecutor::class,
                'db' => $this->getDb()
            ]);
        }
        return $this->_executor;
    }

    /***
     * Get output params
     * Example:
     * [
     *  [
     *     'class' => InputOutputParam::class,
     *     'name' => 'OutputParamName'
     *     'length' => 30,
     *     'type' => PDO::PARAM_STR
     *  ]
     * ]
     * @return array
     */
    public static function specialParams(): array
    {
        return [];
    }

    /**
     * Get procedure name
     * @return string Name of procedure
     */
    public static function procedureName(): string
    {
        return StringHelper::basename(get_called_class());
    }

    /**
     * Event before procedure call
     *
     * @return bool
     */
    protected function beforeCall(): bool
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_CALL, $event);

        return $event->isValid;
    }

    /**
     * Event after procedure call
     *
     * @return mixed
     */
    protected function afterCall($result)
    {
        $event = new AfterCallEvent();
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_CALL, $event);
        return $event->result;
    }

    /***
     * Creates and returns parameters for the procedure
     * Creates InputOutput param for params specified by @param null $attrs
     * @return array|Param[]
     * @see outputParams()
     */
    private function createParams($attrs = null)
    {
        $this->_params = [];
        $values = $this->getAttributes($attrs);

        // Join output params
        $attrs = ArrayHelper::merge($attrs, array_keys(static::specialParams()));

        foreach ($attrs as $attrName) {
            if (array_key_exists($attrName, static::specialParams())) {
                $param = Yii::createObject(static::specialParams()[$attrName]);
            } else {
                $param = new InputParam([
                    'name' => $attrName,
                    'value' => $values[$attrName]
                ]);
            }

            $this->_params[$param->name] = $param;
        }

        return $this->_params;
    }

    /**
     * @param $name
     * @return Param|null
     */
    public function getParam($name)
    {
        return $this->_params[$name] ?? null;
    }

    /**
     * Sets scenario, call events and Procedure::executeInternal()
     *
     * @param string $method Method that should be executed on command (queryOne, queryAll, etc.)
     * @return false|mixed
     */
    protected function execute(string $method)
    {
        $oldScenario = $this->getScenario();
        $this->setScenario(self::SCENARIO_CALL);

        if (!$this->beforeCall()) {
            return false;
        }

        $safeAttrs = $this->safeAttributes();
        Yii::trace(print_r($safeAttrs, true), __METHOD__);
        $result = $this->executeInternal(static::procedureName(), $method, $this->createParams($safeAttrs));

        $this->setScenario($oldScenario);
        return $this->afterCall($result);
    }

    /**
     * @return string Template for command (can contain {procedure} var)
     */
    protected function getCommandTemplate(): string
    {
        return 'SET NOCOUNT ON; EXECUTE [dbo].[{procedure}] {params}';
    }

    /**
     * @return string
     */
    protected function getBeforeCommand(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getAfterCommand(): string
    {
        return '';
    }

    /**
     * Get command based on template
     * @see Procedure::getCommandTemplate()
     * @param array $params Params to command
     * @return string Final command
     */
    private function getCommand(array $params): string
    {
        $cmd = $this->getCommandTemplate();
        $placeholders = [];
        foreach ($params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }
        return ($placeholders === []) ? $cmd : strtr($cmd, $placeholders);
    }

    /**
     * Creates command and executes that command on Procedure::executor.
     * Logs process to Yii debugger.
     *
     * @param string $procName Procedure name
     * @param string $method Method that should be executed on command (queryOne, queryAll, etc.)
     * @param Param[] $params Params for procedure
     * @return mixed Data payload
     */
    protected function executeInternal(string $procName, string $method, array $params = [])
    {
        $cmd = $this->getBeforeCommand() . $this->getCommand(['procedure' => $procName, 'params' => $this->buildInputParams($params)]) . $this->getAfterCommand();
        Yii::trace($cmd, __METHOD__);

        $result = $this->getExecutor()->execute($cmd, $params, $method);

        Yii::trace(print_r($result, true), __METHOD__);
        Yii::trace(print_r($this->_params, true), __METHOD__);
        return $result;
    }

    /**
     * Builds SQL params for procedure
     * @param Param[] $params Params for procedure
     * @return string SQL params
     */
    protected function buildInputParams(array $params): string
    {
        $result = [];

        foreach ($params as $param) {
            $result[] = $param->formatParam();
        }

        return implode(', ', $result);
    }
}