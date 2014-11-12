<?php

class SQLInForLoop
{
    protected $dummy = 'blah';

    function test($users) {

        if(time() == 1234) {
            for($i=0; $i<count($users); $i++)
            {

                if($this->dummy) {
                    $user_roles = Connections::$dbConn->GetAll("SELECT id FROM $users WHERE id = ?", array_values($users));
                }

            }
        }
    }
}
