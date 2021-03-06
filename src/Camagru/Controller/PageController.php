<?php

namespace Camagru\Controller;

use Camagru\Response;

class PageController extends Base\AbstractController
{
    /**
     * @param array $request
     *
     * @return Response
     */
    public function homeAction($request)
    {
        date_default_timezone_set('Europe/Paris');
        if (empty($_SESSION['user']))
            $_SESSION['user'] = null;
        return $this->render('page/home.html.php', ['_request' => $request]);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function notFoundAction($request)
    {
        return $this->render('page/not-found.html.php', ['_request' => $request]);
    }
}