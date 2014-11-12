<?php

class StringInterpolationWithoutBracesInArgument
{
    function test($tableName) {
        $clock = time();
        Connections::$dbConn->GetRow("SELECT id FROM $tableName WHERE role = ?", array('blah'));
        return $clock;
    }
}
