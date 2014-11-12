<?php

class StringConcatenationInArgument
{
    protected $dummy = 'blah';

        function test($tableName, $query_values) {

            Connections::$dbConn->GetRow("SELECT id FROM" . $tableName . "WHERE role = ?", array('blah'));

            Connections::$dbConn->Execute('INSERT into user_news (user_id, news_id, status) values ' . implode(',', $query_values));

            return 'blah';
    }
}
