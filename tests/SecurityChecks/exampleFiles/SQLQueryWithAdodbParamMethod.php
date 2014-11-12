<?php

class SQLQueryWithAdodbParamMethod
{
    function test() {

        $yt_acc_ids = implode(",", Api_Request::$query_get['yt_acc_ids']);

        $rows = Connections::$dbConn->GetAll(
            'SELECT * FROM yt_accounts WHERE FIND_IN_SET (yt_username, ' . Connections::$dbConn->Param('') . ')',
            array($yt_acc_ids)
        );

        return $rows;
    }

    function secondTest($users_ids) {
        $query = '
                    SELECT uca.role_id,u.id, u.first_name, u.last_name
                    FROM `users_channels_access` uca
                    JOIN users u
                        ON u.id = uca.user_id
                    WHERE project_id = ? AND u.id IN (' . Connections::$dbConn->Param('') . ')';

        Connections::$dbConn->GetAll($query, array(111, implode(",", $users_ids)));
    }


    function thirdTest($fb_page_ids, $project_ids) {
        if(AccessMatrix::isUserLevel('master'))
        {
            $this->masterUserReadTags($fb_page_ids);
        }
        elseif(Api_Request::$user['roles'])
        {
            $project_sql = 'SELECT p.channel_id, h.client_id  FROM projects p JOIN hierarchy h ON p.id = h.project_id
                                    WHERE FIND_IN_SET (p.channel_id, ' . Connections::$dbConn->Param('') . ') AND p.channel_type = ?
                                    AND FIND_IN_SET (p.id, ' . Connections::$dbConn->Param('') . ')';

            Connections::$dbConn->getAll($project_sql, array(join(',',$fb_page_ids), 'fb', join(',',$project_ids)));
        }
    }

}
