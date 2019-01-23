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
    
}