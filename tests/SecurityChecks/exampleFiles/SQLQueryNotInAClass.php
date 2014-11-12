<?php

$tableName = $_POST["table"];

$sql = "SELECT id FROM" . $tableName . "WHERE role = ?";
Connections::$dbConn->GetRow($sql, array('blah'));

