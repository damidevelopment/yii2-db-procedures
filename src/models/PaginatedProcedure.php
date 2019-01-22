<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 10:06
 */

namespace damidev\dbprocedures\models;

use Yii;
use yii\db\Exception as DbException;


abstract class PaginatedProcedure extends Procedure
{
    /**
     * @var integer
     */
    public $page = 1;

    /**
     * @var integer
     */
    public $limit = 20;

    /**
     * @var integer
     */
    public $offset = 0;

    /**
     * @var array Filter after call
     */
    public $filter = [];

    /**
     * @var array Data from procedure
     */
    private $_data;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page'], 'integer', 'min' => 1, 'on' => self::SCENARIO_DEFAULT],
            // fix integer values
            [['page', 'limit', 'offset'], 'integerFix', 'on' => self::SCENARIO_DEFAULT],
            //     [['limit', 'offset'], 'safe', 'on' => self::SCENARIO_CALL],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function beforeCall(): bool
    {
        $page = $this->page < 1 ? 1 : $this->page;
        $this->offset = ($page - 1) * $this->limit;
        return parent::beforeCall();
    }

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
                if($item[$key] != $value){
                    return false;
                }
            }
            return true;
        });
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function call()
    {
        return array_slice($this->getAllData(), $this->offset, $this->limit);
    }

    /**
     * @inheritdoc
     */
    public function callCount(): int
    {
        return count($this->getAllData());
    }

}