<?php

namespace Camagru\Controller;

use Camagru\Response;

class CamagruController extends Base\AbstractController
{
    /**
     * @param array $request
     *
     * @return Response
     */
    public function AppCamagruAction($request)
    {
        return $this->render('camagru/appCamagru.html.php', ['_request' => $request]);
    }
}