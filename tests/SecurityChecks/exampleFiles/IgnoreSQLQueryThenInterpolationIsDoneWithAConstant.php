<?php

class InterpolatingClassConstant
{
    const limit = 100;

    public $limit = 100;

    function testWithConstantInFirstArgumentIsSafe() {

        $projects = $this->dbConn->GetAll('SELECT * from users LIMIT ' . self::limit);

        return $projects;
    }

    function testWithConstantInVariableAsFirstArgumentIsSafe() {

        $query = 'SELECT * from users LIMIT ' . self::limit;
        $projects = $this->dbConn->GetAll($query);

        return $projects;
    }

    function testWithVariableIsUnsafe() {

        $projects = $this->dbConn->GetAll('SELECT * from users LIMIT ' . $this->limit);

        return $projects;
    }
}
