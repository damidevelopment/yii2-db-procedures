<?php

namespace damidev\dbprocedures\models;

/**
 * @Author: Jakub Hrášek
 * @Date:   2018-06-18 17:33:48
 */
interface IProcedure
{

    /**
     * Execute procedure
     *
     * @return mixed
     */
    public function call();


}