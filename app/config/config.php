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
            '/'                     => ['controller' => 'page', 'action' => 'home'],
            '/login'                => ['controller' => 'security', 'action' => 'login'],
            '/activate'             => ['controller' => 'security', 'action' => 'activateAccount'],
            '/logout'               => ['controller' => 'security', 'action' => 'logout'],
            '/forgot'               => ['controller' => 'security', 'action' => 'forgot'],
            '/Galerie'              => ['controller' => 'page', 'action' => 'notFound'],
            '/Camagru'              => ['controller' => 'camagru', 'action'  => 'appCamagru']

        ],
    ];
