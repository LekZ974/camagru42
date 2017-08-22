<?php
include __DIR__ . '/parameters.php';

$DB_NAME = 'db_camagru';

$DB_DSN = 'sqlite:'.__DIR__.'/'.$DB_NAME.'.db';

define('DB_DSN', $DB_DSN);
$DB_USER = 'root';
$DB_PASSWORD = 'root';
date_default_timezone_set('Europe/Paris');
$DB_TAB_U = "INSERT INTO users (login, email, password, token, verified, created) VALUES ('Lekz', 'lekz@hotmail.fr', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', 'toto', '1', '".date('Y-m-d')."')";
