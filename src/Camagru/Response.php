<?php

namespace Camagru;


class Response
{
    /**
     * @var string
     */
    protected $content;
    /**
     * @var int
     */
    protected $code;
    /**
     * @var array
     */
    protected $headers;
    /**
     * @param string $content
     * @param int    $code
     * @param array  $headers
     */
    public function __construct($content, $code = 200, array $headers = [])
    {
        $this->content = $content;
        $this->code    = $code;
        $this->headers = $headers;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }
}