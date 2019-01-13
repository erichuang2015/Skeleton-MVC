<?php

namespace Skeleton\Core;

use Skeleton\Core\View;

/**
 * Class for creating http responses
 */
class Response
{
    /** @var array */
    private $headers = [];

    /** @var string */
    private $content = '';

    /** @var integer */
    private $status = 200;

    /** @var View */
    private $viewInstance = null;

    /**
     * @param string $content
     * @param integer $status
     * @param View $viewInstance
     */
    public function __construct($content, $status = 200, View $viewInstance = null)
    {
        $this->viewInstance = $viewInstance;
        $this->content = $content;
        $this->status = $status;
    }

    /**
     * Get Status code
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get all the headers
     *
     * @return void
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the content set by view or json.. etc
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Adds the header to response header array
     *
     * @param string $header
     * @param string $value
     * @return self
     */
    public function header($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * view function helps to create response from content of views
     *
     * @param string|array $view
     * @param array $data
     * @param integer $status
     * @param array $headers
     * @return self
     */
    public function view($view, $data = [], $status = 200, $headers = [])
    {
        $this->content = $this->viewInstance->view($view, $data);
        $this->status = $status;
        $this->headers["Content-Type"] = "text/html";
        $this->headers = \array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Send Json Response with specified hedaers
     *
     * @param array|string $data
     * @param integer $status
     * @param array $headers
     * @return self
     */
    public function json($data = [], $status = 200, $headers = [])
    {
        $this->content = \json_encode($data);
        $this->status = $status;
        $this->headers["Content-Type"] = "application/json";
        $this->headers = \array_merge($this->headers, $headers);
        return $this;
    }
}
