<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 11/07/2018 11:52
 */

namespace damidev\dbprocedures\models;


class MultiProcedure extends Procedure
{

    const SCENARIO_COUNT = 'procedureCount';

    /**
     * @var array Data from procedure
     */
    private $_data;

    /**
     * @var array Filter after call
     */
    public $filter = [];

    /**
     * @var callable
     */
    public $transformCountResult;

    /**
     * @return array Data from procedure
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

    /** Apply filter after call
     * @param array $data
     * @return array
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
     * Get procedure name
     * @return string Name of procedure
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
     * @inheritdoc
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

}