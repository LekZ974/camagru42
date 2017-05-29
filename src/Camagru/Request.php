<?php

namespace Camagru;


class Request
{
    /**
     * @var string
     */
    protected $uri;
    /**
     * @var array
     */
    protected $params;
    /**
     * @var array
     */
    protected $server;
    /**
     * @var string
     */
    protected $path;
    /**
     * @param $server
     */
    public function __construct($server)
    {
        $data = $this->parse($server);

        $this->setServer($server);
        $this->setParams($data['params']);
        $this->setUri($data['uri']);
        $this->setPath($data['path']);
    }
    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
    /**
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }
    /**
     * @param array $server
     *
     * @return $this
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }
    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
    /**
     * @param string $paramName
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    public function get($paramName, $defaultValue = null)
    {
        return isset($this->params[$paramName]) ? $this->params[$paramName] : $defaultValue;
    }
    /**
     * @param array $server
     *
     * @return array
     */
    protected function parse($server)
    {
        $uri    = $server['REQUEST_URI'];
        $request = ['originalUri' => $uri, 'params' => []] + parse_url($uri) + ['query' => null];
        parse_str($request['query'], $request['params']);

        return ['uri' => $uri] + $request;
    }
}