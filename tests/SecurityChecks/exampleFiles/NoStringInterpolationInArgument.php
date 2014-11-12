<?php

class NoStringInterpolationInArgument
{
    protected $dummy = 'blah';

    function test() {
        $clock = time();
        Connections::$dbConn->GetRow("SELECT id FROM users WHERE role = ?", array('blah'));
        return $clock;
    }
}
