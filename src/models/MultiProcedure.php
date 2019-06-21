<?php

namespace damidevelopment\dbprocedures\models;

/**
 * Class MultiProcedure
 * @author Martin Štěpánek
 * @date 07/11/18 11:52 AM
 * @package damidevelopment\dbprocedures\models
 */
class MultiProcedure extends Procedure
{

    /**
     * Scenario that is set up when Procedure::callCount()
     */
    const SCENARIO_COUNT = 'procedureCount';

    /**
     * @var array Data from procedure
     */
    private $_data;

    /**
     * @var array Filter that is applied after call
     */
    public $filter = [];

    /**
     * @var \Closure Transform data from MultiProcedure::callCount()
     * The anonymous function signature should be: `function($result): int`.
     */
    public $transformCountResult;

    /**
     * Returns data from procedure and applies filter
     * @return array Filtered data from procedure
     */
    protected function getAllData()
    {
        if ($this->_data === null) {
            $this->_data = $this->execute('queryAll');
            if ($this->filter) {
                $this->_data = $this->applyFilter($this->_data);
            }
        }
        return $this->_data;
    }

    /**
     * Applies filter after call
     *
     * @param array $data Data payload from procedure
     * @return array Filtered data
     */
    private function applyFilter(array $data): array
    {
        $data = array_filter($data, function ($item) {
            foreach ($this->filter as $key => $value) {
                if ($item[$key] != $value) {
                    return false;
                }
            }
            return true;
        });
        return $data;
    }

    /**
     * Get procedure name that returns count of rows
     *
     * @return string Name of procedure that returns count of rows
     */
    public static function procedureCountName(): string
    {
        return '';
    }


    /**
     * @inheritdoc
     */
    public function call()
    {
        return $this->getAllData();
    }

    /**
     * Returns rows count of procedure data.
     * If is configured, it calls procedure that returns count,
     * otherwise it call count on MultiProcedure::getAllData()
     *
     * @return int Rows count
     */
    public function callCount(): int
    {
        if (static::procedureCountName()) {
            $oldScenario = $this->getScenario();
            $this->setScenario(self::SCENARIO_COUNT);

            $safeAttrs = $this->safeAttributes();
            $result = $this->executeInternal(static::procedureCountName(), 'queryOne', $this->getAttributes($safeAttrs));

            if ($this->transformCountResult && is_callable($this->transformCountResult)) {
                $count = call_user_func($this->transformCountResult, $result);
            } else {
                $count = intval($result);
            }
            $this->setScenario($oldScenario);
            return $count;
        }
        return count($this->getAllData());
    }


    /**
     * Refreshes procedure data
     */
    public function refresh(): void
    {
        $this->_data = null;
    }
}