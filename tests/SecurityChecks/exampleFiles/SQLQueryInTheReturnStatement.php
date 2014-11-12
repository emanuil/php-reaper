<?php

class SQLQueryInTheReturnStatement
{
    protected $dummy = 'blah';

    public $dbTableName = 'blah me';

    public function test($id)
    {
        return Connections::$dbConn->GetRow(
            "SELECT * FROM $this->dbTableName WHERE id = ?",
            array((int) $id)
        );
    }
}
