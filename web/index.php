<?php

function __autoload($class)
{
    include sprintf('%s/../src/%s.php', __DIR__, str_replace('\\', '/', $class));
    session_start();
}

$app = new Camagru();
$app->handleRequest($_SERVER);
