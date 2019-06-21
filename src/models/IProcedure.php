<?php

namespace damidevelopment\dbprocedures\models;

/**
 * Interface IProcedure
 * @author Jakub Hrášek
 * @date 2018-06-18 17:33:48
 * @package damidev\dbprocedures\models
 */
interface IProcedure
{

    /**
     * Executes procedure
     *
     * @return mixed Data payload
     */
    public function call();


}