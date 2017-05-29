<?php
use Camagru\Request;
use Camagru\Response;

class Camagru
{
    /**
     * @param array $server
     */
    public function handleRequest($server)
    {
        $config = include __DIR__ . '/../app/config/config.php';

        $request = new Request($server);

        if (isset($config['routes'][$request->getPath()])) {
            $route = $config['routes'][$request->getPath()];
        } else {
            $route = $config['routes']['*'];
        }


        $class = sprintf("Camagru\\Controller\\%sController", ucfirst($route['controller']));
        $method = sprintf('%sAction', $route['action']);

        $controller = new $class();

        $response = $controller->$method($request);

        if (is_string($response)) {
            $response = new Response($response);
        }

        http_response_code($response->getCode());

        foreach ($response->getHeaders() as $header => $value) {
            header(sprintf('%s: %s', $header, $value));
        }

        echo $response->getContent();
    }
}