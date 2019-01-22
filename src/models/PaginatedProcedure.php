<?php
/**
 * @Author: Martin Štěpánek
 * @Date: 03/07/2018 10:06
 */

namespace damidev\dbprocedures\models;

use Yii;
use yii\db\Exception as DbException;


abstract class PaginatedProcedure extends MultiProcedure
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
     * @var bool If is pagination in API or procedures support pagination
     */
    public $manualPagination = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['page'], 'integer', 'min' => 1, 'on' => self::SCENARIO_DEFAULT],
            // fix integer values
            [['page', 'limit', 'offset'], 'integerFix', 'on' => self::SCENARIO_DEFAULT],
        ];
        if (!$this->manualPagination) {
            $rules[] = [['limit', 'offset'], 'safe', 'on' => self::SCENARIO_CALL];
        }
        return $rules;
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
     * @inheritdoc
     */
    public function call()
    {
        if ($this->manualPagination) {
            return array_slice($this->getAllData(), $this->offset, $this->limit);
        }
        return $this->getAllData();
    }

}