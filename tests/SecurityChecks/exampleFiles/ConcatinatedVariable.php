<?php

class ConcatinatedVariable
{
    protected $dummy = 'blah';

    function test($tableName, $users) {
        $clock = time();
        $blah = 1;

        $sql = "SELECT id FROM" . $tableName . "WHERE role = ?";
        Connections::$dbConn->GetRow($sql, array('blah'));

        for($i=0; $i<count($users); $i++) {
            $blah++;
        }
        return $clock;
    }
}
