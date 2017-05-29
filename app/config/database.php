<?php
include __DIR__ . '/parameters.php';

$DB_DSN = $DB_DRIVER.':'.$DB_PATH;

define('DB_DSN', $DB_DSN);
define('DB_USER', $DB_USER);
define('DB_PASSWORD', $DB_PASSWORD);
