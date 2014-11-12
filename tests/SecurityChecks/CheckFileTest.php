<?php

require_once '../SecurityChecks.php';


class CheckFileTest extends PHPUnit_Framework_TestCase {

    function testStringInterpolationWithoutBracesInArgument() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/StringInterpolationWithoutBracesInArgument.php');

        $this->assertEquals($result, array(7));
    }

    function testStringInterpolationWithBracesInArgument() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/StringInterpolationWithBracesInArgument.php');

        $this->assertEquals($result, array(10, 15));
    }

    function testNoStringInterpolationInArgument() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/NoStringInterpolationInArgument.php');

        $this->assertEquals($result, array());
    }

    function testStringConcatenationInArgument() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/StringConcatenationInArgument.php');

        $this->assertEquals($result, array(9, 11));
    }

    function testStaticStringVariable() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/StaticStringVariable.php');

        $this->assertEquals($result, array());
    }

    function testConcatinatedVariable() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/ConcatinatedVariable.php');

        $this->assertEquals($result, array(12));
    }

    function testSQLInIfStatement() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInIfStatement.php');

        $this->assertEquals($result, array(16));
    }

    function testSQLInElseStatement() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInElseStatement.php');

        $this->assertEquals($result, array(18));
    }

    function testSQLInForLoop() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInForLoop.php');

        $this->assertEquals($result, array(14));
    }

    function testSQLInElseIfStatement() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInElseIfStatement.php');

        $this->assertEquals($result, array(19));
    }


    function testSQLInIfElseIfStatement() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInIfElseIfStatement.php');

        $this->assertEquals($result, array(21));
    }


    function testSQLInIfCondition() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInIfCondition.php');

        $this->assertEquals($result, array(9));
    }

    function testSQLInTernaryOperator() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInTernaryOperator.php');

        $this->assertEquals($result, array(11));
    }

    function testSQLInFunctionWithinIfStatement() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInFunctionWithinIfStatement.php');

        $this->assertEquals($result, array(9));
    }

    function testDoubleVariableAssignment() {
        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/DoubleVariableAssignment.php');

        $this->assertEquals($result, array());
    }


    function testSQLQueryInTheReturnStatement() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLQueryInTheReturnStatement.php');

        $this->assertEquals($result, array(11));
    }

    function testSQLInSwitchStatement() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLInSwitchStatement.php');

        $this->assertEquals($result, array(12));
    }


    function testAdditionToSQLStatementInIfStatement() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/AdditionToSQLStatementInIfStatement.php');

        $this->assertEquals($result, array(17));
    }


    function testIgnoreProperlyCommentedCode() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/IgnoreProperlyCommentedCode.php');

        $this->assertEquals($result, array());
    }


    function testSQLQueryWithAdodbParamMethod() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLQueryWithAdodbParamMethod.php');

        $this->assertEquals($result, array());
    }

    function testSingleConcatenation() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SingleConcatenation.php');

        $this->assertEquals($result, array(10));
    }

    function testSQLQueryNotInAClass() {

        $checks = new SecurityChecks();

        $result = $checks->checkFile('SecurityChecks/exampleFiles/SQLQueryNotInAClass.php');

        $this->assertEquals($result, array(6));
    }
}