<?php

class StaticStringVariable
{
    function test($role_name) {
        $query = 'SELECT id FROM roles WHERE role=? AND scope="system"';
        $role = Connections::$dbConn->GetRow($query, array($role_name));
        return $role['id'];
    }
}
