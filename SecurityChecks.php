<?php

require 'php-parser/lib/bootstrap.php';

ini_set('xdebug.max_nesting_level', 2000);


class SecurityChecks {

    private $dangerousFunctions = array('getone', 'getrow', 'getall', 'getcol', 'getassoc', 'execute', 'replace');
    private $result = array();
    private $methodStatements;
    private $hasPotentialSQLInjection = false;


    public function checkSingleFile($fileName) {

        $result = $this->checkFile($fileName);

        print_r("\n");
        if(count($result) > 0) {

            $this->hasPotentialSQLInjection = true;
            print_r("Potential SQL injections in file $fileName\n");

            foreach ($result as $line) {
                print_r("line: $line\n");
            }

            print_r("\n");
        }

        $this->exitProperly();
    }


    public function checkFile($fileName) {

        $fileStatements = $this->parseFile($fileName);

        foreach($fileStatements as $fileStatement)
        {
            // list all the classes
            if($fileStatement->getType() == 'Stmt_Class')
            {
                foreach($fileStatement->stmts as $classStatement) {

                    // list all methods
                    if($classStatement->getType() == 'Stmt_ClassMethod') {

                        $this->methodStatements = $classStatement->stmts;
                        $this->mainCycle($classStatement);
                    }

                }
            }
            else
            {
                $this->methodStatements = $fileStatements;
                $this->mainCycle($fileStatement);
            }
        }

        return $this->result;
    }


    private function findVariableByName($methodStatements, $variableName, $line) {

        // reverse the statements order so we can start from the one closer to the
        // adodb method

        foreach(array_reverse($methodStatements) as $methodStatement) {
            // we're only interested in the lines above the adodb call
            if($line > $methodStatement->getLine()) {

                $statements = $this->collectAllTheStatements($methodStatement);

                // walk through all if-else-switch statements
                foreach($statements as $statement) {
                    $result = $this->findVariableByName($statement, $variableName, $line);

                    if($result) {
                        return $result;
                    }
                }

                if(is_object($methodStatement)
                    && property_exists($methodStatement, 'var')
                    && property_exists($methodStatement->var, 'name')
                    && $methodStatement->var->name == $variableName) {
                    return $methodStatement;
                }
            }
        }

        // If we can't find how the SQL query is constructed in the current block of code
        // then flag the row for manual check. It's very difficult for simple parser
        // like this to check all the conditions of how the SQL query is constructed prior
        // to sending it to SQL
        return false;
    }


    public function checkDirectory($directoryName) {

        $path = realpath($directoryName);

        print_r("\n");
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($objects as $name => $object) {
            if(!is_dir($name) && strstr($name, '.php') !== false) {

                $dangerousSQLQueries = $this->checkFile($name);

                if(count($dangerousSQLQueries) > 0) {

                    $this->hasPotentialSQLInjection = true;
                    print_r("Potential SQL injections in file $name\n");

                    foreach ($dangerousSQLQueries as $line) {
                        print_r("line: $line\n");
                    }

                    print_r("\n");
                }

                $this->result = array();
            }
        }


        $this->exitProperly();
    }


    private function parseFile($fileName)
    {
        $fileContents = file_get_contents($fileName);
        $fileStatements = array();

        try {

            $parser = new PhpParser\Parser(new PhpParser\Lexer);
            $fileStatements = $parser->parse($fileContents);
            return $fileStatements;

        } catch (PhpParser\Error $exception) {

            echo 'Parse Error: ', $exception->getMessage();
        }

        return $fileStatements;
    }


    private function checkExpressionOrAssign($methodStatement)
    {

        if(is_object($methodStatement)) {
            // Check if the sql query is marked as safe in the comments
            if($this->checkForSafeComments($methodStatement)) {
                return false;
            }

            $methodName = null;

            if ($methodStatement->getType() == 'Expr_MethodCall' && is_string($methodStatement->name)) {
                $methodName = $methodStatement->name;
                $objectToBeInvestigated = $methodStatement;
            } elseif($methodStatement->getType() == 'Expr_Assign' && property_exists($methodStatement->expr, 'name') && is_string($methodStatement->expr->name)) {
                $methodName = $methodStatement->expr->name;
                $objectToBeInvestigated = $methodStatement->expr;
            }

            if(in_array(strtolower($methodName), $this->dangerousFunctions)) {
                if(!empty($objectToBeInvestigated->args[0])) {
                    $this->checkTheArgument($methodStatement, $objectToBeInvestigated);
                }
            }
        }
    }


    private function mainCycle($classStatement)
    {
        if(is_object($classStatement)) {
            if(property_exists($classStatement, 'stmts') && is_array($classStatement->stmts)) {

                foreach ($classStatement->stmts as $methodStatement) {

                    if($methodStatement->getType() != 'Expr_MethodCall'
                        && $methodStatement->getType() != 'Expr_Assign') {

                        $this->checkNonAssignAndMethodCallStatements($methodStatement);

                        // The ternary operator
                    }elseif($methodStatement->getType() == 'Expr_Assign'
                        && $methodStatement->expr->getType() == 'Expr_Ternary') {

                        $this->checkTernaryOperator($methodStatement);
                    }

                    else {
                        $this->checkExpressionOrAssign($methodStatement);
                    }
                }
            }
            else
            {
                $this->checkExpressionOrAssign($classStatement);
            }
        }


    }


    private function investigateVariable($methodStatement, $argumentName)
    {

        $variable = $this->findVariableByName($this->methodStatements, $argumentName, $methodStatement->getLine());

        // If we can't find the variable definition in the current method then flag this for manual review
        if (!$variable) {
            $this->result[] = $methodStatement->getLine();

        } //This is needed for constructions such as newSQL = oldSQL = "Vulnerable Query", and then newSQL is used as the first argument
        elseif (property_exists($variable, 'expr') && property_exists($variable->expr, 'expr')) {

            $this->checkForDoubleAssignment($methodStatement, $variable);

        } // this is the Param() binding
        elseif ($variable->expr->getType() == 'Expr_BinaryOp_Concat') {

            $this->checkConcatMethod($methodStatement, $variable);


        } // the string is not constant
        else {
            $this->checkForRegularString($methodStatement, $variable);
        }
    }


    private function checkTheArgument($methodStatement, $objectToBeInvestigated)
    {
        //the name and type of the first argument
        $argumentType = $objectToBeInvestigated->args[0]->value->getType();


        if ($argumentType == 'Expr_BinaryOp_Concat') {
            if (
                // this is a call to the Param() method which is safe
                //(property_exists($objectToBeInvestigated->args[0]->value->left, 'right') && $objectToBeInvestigated->args[0]->value->left->right->getType() == 'Expr_MethodCall' && $objectToBeInvestigated->args[0]->value->left->right->name != 'Param')
                (property_exists($objectToBeInvestigated->args[0]->value->left, 'right') && $objectToBeInvestigated->args[0]->value->left->right->getType() == 'Expr_ArrayDimFetch')
                || ($objectToBeInvestigated->args[0]->value->right->getType() != 'Scalar_String' && $objectToBeInvestigated->args[0]->value->right->getType() != 'Expr_Cast_Int')


            ) {
                $this->result[] = $methodStatement->getLine();
            }


            $this->checkIfWeAreInterpolatingWithMethodOtherThanParam($methodStatement, $objectToBeInvestigated);

            $this->checkIfTheInterpolatedVariableIsDangerous($methodStatement, $objectToBeInvestigated);

            $this->checkIfWeAreInterpolatingWithADangerousMethod($methodStatement, $objectToBeInvestigated);
        }

        if ($argumentType == 'Scalar_Encapsed') {
            $this->result[] = $methodStatement->getLine();
        }


        if ($argumentType == 'Expr_Variable') {
            $argumentName = $objectToBeInvestigated->args[0]->value->name;
            $this->investigateVariable($methodStatement, $argumentName);
        }
    }


    private function checkTheIfStatement($methodStatement)
    {
        if ($methodStatement->else) {
            $this->mainCycle($methodStatement->else);
        }

        if ($methodStatement->elseifs) {
            $this->mainCycle($methodStatement->elseifs[0]);
        }


        if (property_exists($methodStatement->cond, 'right')) {
            $this->checkExpressionOrAssign($methodStatement->cond->right);
        }

        if (property_exists($methodStatement->cond, 'left')) {
            $this->checkExpressionOrAssign($methodStatement->cond->left);
        }


        if (property_exists($methodStatement->cond, 'left') && $methodStatement->cond->left->getType() == 'Expr_FuncCall') {

            if ($methodStatement->cond->left->args) {
                $this->checkExpressionOrAssign($methodStatement->cond->left->args[0]->value);
            }
        }
    }


    private function collectAllTheStatements($methodStatement)
    {
        $statements = array();

        if (!empty($methodStatement->stmts)) {

            $statements[] = $methodStatement->stmts;
        }

        if (!empty($methodStatement->cases)) {
            $statements[] = $methodStatement->cases;
        }


        if (!empty($methodStatement->else)) {
            $statements[] = $methodStatement->else->stmts;
        }

        if (!empty($methodStatement->elseifs[0])) {
            $statements[] = $methodStatement->elseifs[0]->stmts;
        }

        if (isset($methodStatement->elseifs[0])) {
            $statements[] = $methodStatement->elseifs[0]->stmts;
        }
        return $statements;
    }


    private function checkForSafeComments($methodStatement)
    {
        if (is_array($methodStatement->getAttribute('comments'))) {

            foreach ($methodStatement->getAttribute('comments') as $comment) {

                $reflector = new \ReflectionClass($comment);
                $classProperty = $reflector->getProperty('text');
                $classProperty->setAccessible(true);
                $commentText = $classProperty->getValue($comment);

                if (strpos(strtolower($commentText), 'safesql') !== false) {
                    return true;
                }
            }
        }
    }


    private function checkConcatMethod($methodStatement, $variable)
    {
        if ((property_exists($variable->expr->left, 'right')
                && $variable->expr->left->right->name != 'Param') || (!property_exists($variable->expr->left, 'right'))
        ) {
            $this->result[] = $methodStatement->getLine();
        }
    }


    private function checkForDoubleAssignment($methodStatement, $variable)
    {
        if ($variable->expr->getType() != 'Scalar_String' && $variable->expr->expr->getType() != 'Scalar_String') {
            $this->result[] = $methodStatement->getLine();
        }
    }


    private function checkForRegularString($methodStatement, $variable)
    {
        if ($variable->expr->getType() != 'Scalar_String') {
            $this->result[] = $methodStatement->getLine();
        }
    }


    private function checkSwitchStatement($methodStatement)
    {
        foreach ($methodStatement->cases as $switchCase) {
            $this->mainCycle($switchCase);
        }
    }


    private function checkTernaryOperator($methodStatement)
    {
        $this->checkExpressionOrAssign($methodStatement->expr->if);
        $this->checkExpressionOrAssign($methodStatement->expr->else);
    }


    private function checkNonAssignAndMethodCallStatements($methodStatement)
    {
        // Drill deep down if there are more statements
        if (property_exists($methodStatement, 'stmts')) {
            $this->mainCycle($methodStatement);
        }

        if ($methodStatement->getType() == 'Stmt_If') {
            $this->checkTheIfStatement($methodStatement);
        }

        // The switch statements
        if ($methodStatement->getType() == 'Stmt_Switch') {
            $this->checkSwitchStatement($methodStatement);
        }

        // The sql method is in the returned directly: e.g. return GetAll(...);
        if (property_exists($methodStatement, 'expr') && $methodStatement->getType() == 'Stmt_Return') {
            if(!$this->checkForSafeComments($methodStatement)) {
                $this->checkExpressionOrAssign($methodStatement->expr);
            }

        }
    }

    // this is needed so the CI can pick the failure and fail the job
    protected function exitProperly()
    {
        if ($this->hasPotentialSQLInjection) {
            exit(-1);
        } else {
            exit(0);
        }
    }


    private function checkIfWeAreInterpolatingWithADangerousMethod($methodStatement, $objectToBeInvestigated)
    {
        if (property_exists($objectToBeInvestigated->args[0]->value->left, 'left') &&
            property_exists($objectToBeInvestigated->args[0]->value->left->left, 'right') &&
            $objectToBeInvestigated->args[0]->value->left->left->right->getType() == 'Expr_FuncCall'
        ) {
            if ($objectToBeInvestigated->args[0]->value->left->left->right->name->parts[0] != 'date') {
                $this->result[] = $methodStatement->getLine();
            }
        }
    }


    private function checkIfTheInterpolatedVariableIsDangerous($methodStatement, $objectToBeInvestigated)
    {
        if (property_exists($objectToBeInvestigated->args[0]->value->left, 'right')
            && $objectToBeInvestigated->args[0]->value->left->right->getType() == 'Expr_Variable'
        ) {

            $this->investigateVariable($methodStatement, $objectToBeInvestigated->args[0]->value->left->right->name);
        }
    }

    /**
     * @param $methodStatement
     * @param $objectToBeInvestigated
     */
    private function checkIfWeAreInterpolatingWithMethodOtherThanParam($methodStatement, $objectToBeInvestigated)
    {
        if ((property_exists($objectToBeInvestigated->args[0]->value->left, 'right') && $objectToBeInvestigated->args[0]->value->left->right->getType() == 'Expr_MethodCall' && $objectToBeInvestigated->args[0]->value->left->right->name != 'Param')) {
            $this->result[] = $methodStatement->getLine();
        }
    }
}