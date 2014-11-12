<?php

class SQLInSwitchStatement
{
    function test($role) {


        switch ($role){
            case self::REL_CHANNEL: {
                if(AccessMatrix::isUserLevel(ACC_MASTER))
                {
                    $data = Connections::$dbConn->GetAll(
                        "SELECT * FROM projects_templates WHERE channel_project_id IN (". Api_Request::$query_get['project_ids'] .")"
                    );
                }
                else
                {

                    $query = 'SELECT DISTINCT t2 . *
    							FROM projects_templates AS t1
    							INNER JOIN hierarchy AS h ON t1.channel_project_id = h.project_id
    							INNER JOIN projects_templates AS t2 ON t1.template_project_id = t2.template_project_id
    							WHERE h.agency_id = ?';
                    Connections::$dbConn->GetRow($query, array($role['agency_id']));

                }
            }

        }
    }
}

