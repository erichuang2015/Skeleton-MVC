<?php

namespace Skeleton\Core;

/**
 * Class representation of Http request
 */
class Request
{
    /**
     * @var string
     */
    private $serverBasePath = null;

    /**
     * @var array
     */
    private $requestHeaders = [];

    /**
     * Input instnace for handeling suprglobals
     *
     * @var Input
     */
    private $inputInstance;

    public function __construct($server = [], $get = [], $post = [], $files = [], $cookie = [])
    {
        $this->inputInstance = new Input($server, $get, $post, $files, $cookie);
    }

    /**
     * Call input class method as if it yours
     */
    public function __call($name, $arguments)
    {
        return $this->inputInstance->$name(isset($arguments[0]) ? $arguments[0] : null);
    }

    /**
     * Type of method submitted i.e. get, post, etc.
     *
     * @return string
     */
    public function method()
    {
        $method = $this->server('REQUEST_METHOD');
        
        // If method is head then don't output anything and chnage it to GET
        if ($method == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($method == 'POST') {
            // Check for header overrides like put, patch, delete
            $headers = $this->headers();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * Request headers similar to getallheaders function
     *
     * @return array
     */
    public function headers()
    {
        if (empty($this->requestHeaders)) {
            foreach ($this->server() as $name => $value) {
                if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                    $this->requestHeaders[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }
        return $this->requestHeaders;
    }

    /**
     * Current uri i.e. part after www.example.com/
     *
     * @return string
     */
    public function path()
    {
        // get current uri and remove the base path form it
        $uri = substr($this->server('REQUEST_URI'), strlen($this->basePath()));

        // Remove the get params
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/'.trim($uri, '/');
    }
    
    /**
     * Base path of applicaton if application is deployed under directory
     *
     * @return string
     */
    public function basePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $this->server('SCRIPT_NAME')), 0, -1)).'/';
        }
        return $this->serverBasePath;
    }
}
