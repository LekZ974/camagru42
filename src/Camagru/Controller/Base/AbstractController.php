<?php

namespace Camagru\Controller\Base;

use Camagru\Response;
use Camagru\Database;

abstract class AbstractController
{
    /**
     * @param string $view
     * @param array  $params
     *
     * @return string
     */
    protected function renderView($view, array $params = [])
    {
        $date = new \DateTime();
        $identity = $_SESSION['user'];
        ob_start();
        extract($params);
        include sprintf('%s/../../Resources/views/%s', __DIR__, $view);
        $content = ob_get_clean();

        if (isset($layout)) {
            $content = $this->renderView(sprintf('%s', $layout), ['content' => $content]);
        }

        return $content;
    }
    /**
     * @param string $view
     * @param array  $params
     * @param int    $code
     * @param array  $headers
     *
     * @return Response
     */
    protected function render($view, array $params = [], $code = 200, $headers = [])
    {
        return new Response($this->renderView($view, $params), $code, $headers);
    }
}