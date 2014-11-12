<?php

class SQLInIfCondition
{
    protected $dummy = 'blah';

    function test($users) {

        if(time() && Connections::$dbConn->GetAll("SELECT id FROM $users WHERE id = ?", array_values($users))) {
            return true;
        }

        return false;
    }

}
