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
            '/reset'                => ['controller' => 'security', 'action' => 'resetPassword'],
            '/logout'               => ['controller' => 'security', 'action' => 'logout'],
            '/forgot'               => ['controller' => 'security', 'action' => 'forgot'],
            '/Camagru'              => ['controller' => 'camagru', 'action'  => 'appCamagru'],
            '/gallery'              => ['controller' => 'camagru', 'action' => 'gallery'],
            '/user-gallery'         => ['controller' => 'camagru', 'action' => 'userGallery'],
            '/mini-gallery'         => ['controller' => 'camagru', 'action' => 'miniGallery'],
            '/save'                 => ['controller' => 'camagru', 'action' => 'save'],
            '/delete'               => ['controller' => 'camagru', 'action' => 'delete'],
            '/comments'             => ['controller' => 'camagru', 'action' => 'comments'],
            '*'                     => ['controller' => 'page', 'action' => 'notFound'],

        ],
    ];
