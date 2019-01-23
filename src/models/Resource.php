<?php

namespace damidev\dbprocedures\models;

use yii\base\Model;

/**
 * Class Resource
 * @author Jakub Hrášek
 * @date 2018-06-21 13:49:54
 * @package damidev\dbprocedures\models
 */
class Resource extends Model
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    // TODO: move formatters to its own helper class

    /**
     * @param  mixed $dateValue
     * @return string formatted date
     */
    protected static function formatDate($dateValue)
    {
        return $dateValue instanceof \DateTimeInterface
            ? $dateValue->format('Y-m-d H:i:s')
            : $dateValue;
    }
}