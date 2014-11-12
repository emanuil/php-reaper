<?php

class DoubleVariableAssignment
{
    protected $dummy = 'blah';

    function test($query) {

        $sql_user1 = $sql_user = 'SELECT id, first_name, last_name, title, email, phone, mobile, is_test_user FROM users WHERE email = ?';
        $user = Connections::$dbConn->GetRow($sql_user1, array($query['email']));
    }
}

