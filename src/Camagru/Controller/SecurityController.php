<?php

namespace Camagru\Controller;

use Camagru\Response;

class SecurityController extends Base\AbstractController
{
    /**
     * @param array $request
     *
     * @return Response
     */
    public function loginAction($request)
    {
        return $this->render('security/login.html.php', []);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function logoutAction($request)
    {
        return $this->render('security/logout.html.php', []);
    }
}
