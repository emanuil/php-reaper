<?php

class AdditionToSQLStatementInIfStatement
{

    public $_filter_client_ids = array();

    public function test($id)
    {
        $sql = "SELECT cli.* FROM clients AS cli WHERE agency_id = ?";

        if (count($this->_filter_client_ids))
        {
            $sql .= " AND id IN (".join(',', $this->_filter_client_ids).")";
        }

        $clients = Connections::$dbConn->GetAll($sql, array($id));

        return $clients;
    }
}




