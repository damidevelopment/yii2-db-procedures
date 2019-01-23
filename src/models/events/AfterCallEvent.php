<?php

namespace damidev\dbprocedures\models\events;

use yii\base\Event;

/**
 * Event that is called after Procedure::call()
 *
 * Class AfterCallEvent
 * @author Jakub Hrášek
 * @date 2018-06-28 19:34:46
 * @package damidev\dbprocedures\models\events
 */
class AfterCallEvent extends Event
{

    /**
     * @var mixed
     */
    public $result;

}