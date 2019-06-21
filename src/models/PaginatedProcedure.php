<?php

namespace damidevelopment\dbprocedures\models;

use Yii;

/**
 * Class PaginatedProcedure
 * @author Martin Štěpánek
 * @date 7/3/18 10:06 PM
 * @package damidev\dbprocedures\models
 */
abstract class PaginatedProcedure extends MultiProcedure
{

    /**
     * @var integer Number of page
     */
    public $page = 1;

    /**
     * @var integer Number of rows per page
     */
    public $limit = 20;

    /**
     * @var integer Offset of page
     */
    public $offset = 0;

    /**
     * @var bool If is pagination in API or procedure support pagination
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
     * PaginatedProcedure::manualPagination is true,
     * it slices data by PaginatedProcedure::offset and PaginatedProcedure::limit
     *
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