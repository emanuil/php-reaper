<?php

class SQLInTernaryOperator
{
    protected $dummy = 'blah';

    function test($sql_gp_accounts_ids, $users) {

        $sql_gp_accounts_ids = "select * from $users where 'blah' =1";

        $gp_accounts_ids = isset($sql_gp_accounts_ids) ? Connections::$dbConn->GetAll($sql_gp_accounts_ids) : array();

        return $gp_accounts_ids;
    }
}
