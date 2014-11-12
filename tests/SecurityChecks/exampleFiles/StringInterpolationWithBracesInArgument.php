<?php

class StringInterpolationWithBracesInArgument
{



    function test($tableName, $clientId) {

        Connections::$dbConn->GetOne(
            "SELECT multiple_ad_accounts FROM {$this->_dbTableName} WHERE client_id = ?",
            array($clientId)
        );

        $result = Connections::$dbConn->GetOne("SELECT id FROM {$tableName} WHERE role = \"owner\" AND scope=\"system\"");

        if($result) {
            echo "Blah";
        }

    }

}
