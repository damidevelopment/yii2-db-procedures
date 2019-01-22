<?php

namespace damidev\dbprocedures\models;

use app\models\events\AfterCallEvent;
use Yii;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\db\Connection;
use yii\helpers\StringHelper;


/**
 * @Author: Jakub Hrášek
 * @Date:   2018-06-13 18:02:19
 */
abstract class Procedure extends Model implements ProcedureInterface
{
    const EVENT_BEFORE_CALL = 'beforeCall';
    const EVENT_AFTER_CALL = 'afterCall';

    const SCENARIO_CALL = 'procedureCall';

    /**
     * @var Connection
     */
    private $_db;


    /**
     * @param Connection
     * @return self
     */
    public function setDb(Connection $db): self
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * @return Connection
     */
    public function getDb(): Connection
    {
        if ($this->_db === null) {
            $this->_db = Yii::$app->getDb();
        }
        return $this->_db;
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
     * Event
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

    /**
     * @param  string query method name, possible values are queryOne, queryAll
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
        $result = $this->executeInternal(static::procedureName(), $method, $this->getAttributes($safeAttrs));

        Yii::trace(print_r($result, true), __METHOD__);

        $this->setScenario($oldScenario);
        return $this->afterCall($result);
    }

    /**
     * @return string Template for command (can containt {procedure} var)
     */
    protected function getCommandTemplate(): string
    {
        return 'SET NOCOUNT ON; EXECUTE [dbo].[{procedure}]';
    }

    /** Get command by template
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
     * @param string $procName
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function executeInternal(string $procName, string $method, array $params = [])
    {
        $cmd = $this->getCommand(['procedure' => $procName]) . ' ' . $this->buildInputParams($params);
        Yii::trace($cmd, __METHOD__);

        $command = $this->getDb()->createCommand($cmd);

        // bind params
        foreach ($params as $attr => $value) {
            $command->bindValue(':' . $attr, $value);
        }
        $result = call_user_func([$command, $method]);

        Yii::trace(print_r($result, true), __METHOD__);
        return $result;
    }

    /**
     * @param  array
     * @return string
     */
    protected function buildInputParams(array $params): string
    {
        $result = [];

        foreach ($params as $attr => $value) {
            $result[] = '@' . $attr . ' = :' . $attr;
        }

        return implode(', ', $result);
    }

    /**
     * TODO move to better class
     *
     * @param  string attribute name
     * @return void
     */
    public function integerFix(string $attr): void
    {
        $this->$attr = intval($this->$attr);
    }
}