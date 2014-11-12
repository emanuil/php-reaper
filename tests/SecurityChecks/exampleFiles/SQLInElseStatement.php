<?php

class SQLInElseStatement
{
    protected $dummy = 'blah';

    function test($users) {

            if(count($users) > 0) {
                return false;
            }
            else {

                $where_clauses_roles['user_id=?'] = $users['id'];
                $sql_user_roles = 'SELECT *
                                   FROM user_roles
                                   WHERE ' . join(' AND ', array_keys($where_clauses_roles));
                $user_roles = Connections::$dbConn->GetAll($sql_user_roles, array_values($where_clauses_roles));

            }
    }
}
