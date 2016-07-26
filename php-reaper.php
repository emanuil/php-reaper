#!/usr/bin/env php

<?php

require 'SecurityChecks.php';

$options = getopt("d:f:");

if(empty($options)) {
    echo "Usage: php-reaper -d directory -f file\n";
    echo "-d directory: the directory to check\n";
    echo "-f directory: the file to check\n";
    echo "\n";

    exit(-1);
}

$checks = new SecurityChecks();

if(isset($options['d'])) {
    $checks->checkDirectory($options['d']);
}

if(isset($options['f'])) {
    $checks->checkSingleFile($options['f']);
}

