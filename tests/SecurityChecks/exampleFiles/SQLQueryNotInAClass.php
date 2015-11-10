<?php

$tableName = $_POST["table"];
// vulnerable
$sql = "SELECT id FROM" . $tableName . "WHERE role = ?";
Connections::$dbConn->GetRow($sql, array('blah'));


$projectsWithTracking = array(1, 2, 3);
$newChannelTrackingTable = 'channel_tracking';


foreach($projectsWithTracking as $project)
{
    // not vulnerable
    Connections::$dbConn->Execute("
        INSERT INTO ".$newChannelTrackingTable." (project_id, has_tracking, type, sitecatalyst_delimiter)
        VALUES (?, '1', 'google', '|')",
        array((int)$project['project_id'])
    );
}
