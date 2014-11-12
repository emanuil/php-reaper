<?php

class SingleConcatenation
{
    protected $from = array();

    function test() {
        $connection = $this->from['conn'];

        $connection->GetAll("SELECT * FROM table" .  $this->from['where_order_limit']);
    }
}
