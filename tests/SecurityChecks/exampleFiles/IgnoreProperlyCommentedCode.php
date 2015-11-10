<?php

class IgnoreProperlyCommentedCode
{
    protected $dummy = 'blah';

    function singleLineComment($tableName, $users) {
        $clock = time();
        $blah = 1;

        $sql = "SELECT id FROM" . $tableName . "WHERE role = ?";
        // no
        // safesql blah
        // bahur
        Connections::$dbConn->GetRow($sql, array('blah'));

        for($i=0; $i<count($users); $i++) {
            $blah++;
        }
        return $clock;
    }


    function multipleLinesComment($tableName, $users) {

        $sql = "SELECT id FROM" . $tableName . "WHERE role = ?";
        /* no
         safesql blah
         bahur */

        $data = Connections::$dbConn->GetRow($sql, array('blah'));

        return $data;


    }

    function commentBeforeReturnStatement($tableName, $users) {

        $sql = "SELECT id FROM" . $tableName . "WHERE role = ?";

        //safesql
        return Connections::$dbConn->GetRow($sql, array('blah'));


    }
}
