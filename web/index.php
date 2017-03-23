<?php

function __autoload($class)
{
    include sprintf('%s/../src/%s.php', __DIR__, str_replace('\\', '/', $class));
}

$app = new Camagru();
$app->handleRequest($_SERVER);
