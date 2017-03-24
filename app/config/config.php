<?php
//CONFIG ECOLE 42
//return (include __DIR__ . '/parameters.php') + [
//        'routes' => [
//            '/web/'           => ['controller' => 'page', 'action'     => 'home'],
//            '/web/login'      => ['controller' => 'security', 'action' => 'login'],
//            '/web/logout'     => ['controller' => 'security', 'action' => 'logout'],
//            '/web/*'          => ['controller' => 'page', 'action'     => 'notFound'],
//            '/web/appCamagru' => ['controller' => 'camagru', 'action'  => 'appCamagru']
//        ],
//    ];

//CONFIG MAISON
return (include __DIR__ . '/parameters.php') + [
        'routes' => [
            '/'       => ['controller' => 'page', 'action' => 'home'],
            '/login'  => ['controller' => 'security', 'action' => 'login'],
            '/logout' => ['controller' => 'security', 'action' => 'logout'],
            '*'       => ['controller' => 'page', 'action' => 'notFound'],
            '/appCamagru' => ['controller' => 'camagru', 'action'  => 'appCamagru']

        ],
    ];
