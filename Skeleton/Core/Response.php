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
     * @param array $headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->headers = $headers;
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
}
