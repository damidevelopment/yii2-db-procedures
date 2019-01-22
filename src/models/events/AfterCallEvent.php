<?php

namespace damidev\dbprocedures\models\events;

use yii\base\Event;


/**
 * @Author: Jakub Hrášek
 * @Date:   2018-06-28 19:34:46
 */
class AfterCallEvent extends Event
{

    /**
     * @var mixed
     */
    public $result;

}