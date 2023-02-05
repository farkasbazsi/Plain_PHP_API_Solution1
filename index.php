<?php

require('./secrets.php');
$pdo = new PDO('mysql:host=localhost;dbname=' . $secrets['mysqlDb'], $secrets['mysqlUser'], $secrets['mysqlPass']);

if ($development) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$resource = strtok($_SERVER['QUERY_STRING'], '=');

if ($resource == 'users') {
    require('users.php');
    echo "HI";
}
if ($resource == 'parcels') {
    require('parcels.php');
}

echo json_encode($data);