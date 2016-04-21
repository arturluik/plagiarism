<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "test";

$dbh = new PDO("pgsql:dbname=plagiarism;host=localhost", "plagiarism", "sandbox");


