<?php

namespace Framework\Core;

final class Router
{
    /**
     * Internal index of arrays
     *
     * @var array
     */
    private $routes = array();

    /**
     * Allowed router methods
     *
     * @var array
     */
    private $routerMethods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');

    /**
     * 404 Callback
     *
     * @var string
     */
    private $notFoundCallback;

    /**
     * Server base path
     *
     * @var string
     */
    private $serverBasePath = null;
    
    /**
     * Call to route methods such as get, post, etc...
     *
     * @param string $pattern
     * @param mixed $fn
     * @return void
     */
    public function __call($method, $arguments)
    {
        $this->match($method, $arguments[0], $arguments[1]);
    }

    public function any($pattern, $fn)
    {
        $this->match(['GET', 'PUT', 'PATCH', 'POST', 'DELETE', 'OPTIONS'], $pattern, $fn);
    }

    public function resource($pattern, $fn)
    {
        // TODO:: resource route
    }

    /**
     * Add the route to the routing array
     *
     * @param mixed $methods
     * @param string $pattern
     * @param string $fn
     * @return
     */
    public function match($methods, $pattern, $fn)
    {
        foreach ((array)$methods as $method) {
            if (in_array(strtoupper($method), $this->routerMethods)) {
                $this->routes[strtoupper($method)][] = array(
                    'pattern' => $pattern,
                    'fn' => $fn
                );
            }
        }
    }

    /**
     * Start the router instance
     *
     * @return bool whether or not route was hadled
     */
    public function run()
    {
        $numHandled = 0;
        $requestMethod = $this->getRequestMethod();

        // Handle route for requested method
        if (isset($this->routes[$requestMethod])) {
            $numHandled = $this->handle($this->routes[$requestMethod]);
        }

        // call for 404 callback if its present
        if ($numHandled === 0) {
            if ($this->notFoundCallback) {
                $this->invoke($this->notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            }
        }

        // if it was head method clean up
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        return $numHandled !== 0;
    }

    /**
     * Set the 404 method
     *
     * @param string $fn
     * @return void
     */
    public function set404($fn)
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * Find and call the callback for current route
     *
     * @param array $routes
     * @param boolean $quitAfterRun
     * @return int Number of routes handled
     */
    private function handle($routes, $quitAfterRun = true)
    {
        $numHandled = 0;
        $uri = $this->getCurrentUri();

        // run over every route and find the matching one
        foreach ($routes as $route) {
            $route['pattern'] = preg_replace('/{([A-Za-z]*?)}/', '(\w+)', $route['pattern']);
            if (preg_match_all('#^'.$route['pattern'].'$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                // Remove matched part in uri except params
                $matches = array_slice($matches, 1);

                // extract parameters from request
                $params = array_map(function ($match, $index) use ($matches) {
                    // We have following parameter: take substring from current param position until next one
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    } else {
                        // We have no following paramters return whole
                        return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                    }
                }, $matches, array_keys($matches));

                // if pattern matches call the related function
                $this->invoke($route['fn'], $params);
                
                $numHandled++;
                
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        return $numHandled;
    }

    /**
     * Returns the server request method
     *
     * @return string
     */
    protected function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // If method is head then don't output anything and chnage it to GET
        if ($method == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($method == 'POST') {
            // Check for header overrides like put, patch, delete
            $headers = $this->getRequestHeaders();
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
    public function getRequestHeaders()
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
    protected function getCurrentUri()
    {
        // get current uri and remove the base path form it
        $uri = substr($_SERVER['REQUEST_URI'], strlen($this->getBasePath()));

        // Remove the get params
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/'.trim($uri, '/');
    }
    
    /**
     * Invoke the given function with parameters
     *
     * @param string $fn
     * @param array $params
     * @return void
     */
    private function invoke($fn, $params = array())
    {
        // check if its a callback if then call it
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            // laravel like Controller@method routing
            list($controller, $method) = explode('@', $fn);
            $controller = 'App\\Controllers\\'.$controller;
            call_user_func_array(array(new $controller(), $method), $params);
        }
    }

    /**
     * returns the base path
     * @return string
     */
    protected function getBasePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)).'/';
        }
        return $this->serverBasePath;
    }
}
