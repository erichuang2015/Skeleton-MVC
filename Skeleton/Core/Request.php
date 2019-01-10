<?php

namespace Skeleton\Core;

/**
 * Class representation of Http request
 */
class Request extends Input
{
    /**
     * Server base path
     *
     * @var string
     */
    private $serverBasePath = null;
    
    public function __construct()
    {
        // TODO: Accept request properties like $_SERVER, protocol, etc.
    }

    /**
     * Returns the server request method
     *
     * @return string
     */
    public function method()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
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
     * Returns the request headers
     *
     * @return array
     */
    public function headers()
    {
        // getallheaders is defined in helpers
        $headers = getallheaders();
        return $headers !== false ? $headers : array();
    }

    /**
     * Returns the current uri
     *
     * @return void
     */
    public function path()
    {
        // get current uri and remove the base path form it
        $uri = substr($_SERVER['REQUEST_URI'], strlen($this->basePath()));

        // Remove the get params
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/'.trim($uri, '/');
    }
    
    /**
     * Returns the base path
     * @return string
     */
    public function basePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)).'/';
        }
        return $this->serverBasePath;
    }
}
