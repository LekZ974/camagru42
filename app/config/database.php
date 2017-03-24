<?php
include __DIR__ . '/parameters.php';

$DB_DSN = $DB_DRIVER.':'.$DB_PATH.';dbname='.$DB_NAME;
$DB_USER = 'DB_USER';
$DB_PASSWORD = 'DB_PASS';
$DB_TABLE = [
    'users' => 'users',
    'pictures' => 'pictures',
    'comments' => 'comments',
    'likes' => 'likes'
];
