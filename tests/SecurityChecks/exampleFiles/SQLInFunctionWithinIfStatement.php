<?php

class SQLInFunctionWithinIfStatement
{
    protected $dummy = 'blah';

    function test($sql_user_roles) {

        if(count(Connections::$dbConn->GetAll($sql_user_roles)) > 0) {
            return false;
        }
        else {
            return true;
        }
    }
}
