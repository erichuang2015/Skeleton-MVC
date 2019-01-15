<?php

namespace Skeleton\Core;

/**
 * Router Class stores, matches and calls routing related actions
 */
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

    /** @var Request */
    private $request = null;

    /** @var Response */
    private $response = null;
   
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Call to route methods such as get, post, etc...
     *
     * @param string $method which http method to handle
     * @param array $arguments pattern and function to be handled
     * @return void
     */
    public function __call($method, $arguments)
    {
        $befores = [];
        if (isset($arguments[2])) {
            $befores = $arguments[2];
        }
        $this->match($method, $arguments[0], $arguments[1], $befores);
    }

    /**
     * Matches any method on a given pattern
     *
     * @param string $pattern
     * @param string $fn
     * @return void
     */
    public function any($pattern, $fn, $befores = [])
    {
        return $this->match(['GET', 'PUT', 'PATCH', 'POST', 'DELETE', 'OPTIONS'], $pattern, $fn, $befores);
    }

    //public function resource($pattern, $fn)
    //{
    // TODO:: resource route
    //}

    /**
     * Add the route to the routing array
     *
     * @param mixed $methods    Http Methods
     * @param string $pattern   Uri
     * @param string $fn        Callback
     * @param array $befores    Middleware
     * @return
     */
    public function match($methods, $pattern, $fn, $befores = [])
    {
        foreach ((array)$methods as $method) {
            if (in_array(strtoupper($method), $this->routerMethods)) {
                $this->routes[strtoupper($method)][] = array(
                    'pattern' => $pattern,
                    'fn' => $fn,
                    'befores' => $befores
                );
            }
        }
    }

    /**
     * Start the router instance
     * returns the value from Callback
     *
     * @return Response|false
     */
    public function run()
    {
        $numHandled = 0;
        $requestMethod = $this->request->method();

        // TODO: implement middleware handling

        // Handle route for requested method
        if (isset($this->routes[$requestMethod])) {
            $numHandled = $this->handle($this->routes[$requestMethod]);
        }

        // call for 404 callback if its present
        if ($numHandled === 0) {
            if ($this->notFoundCallback) {
                $this->response = $this->invoke($this->notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            }
        }

        // if it was head method clean up
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        return $this->response ?: false;
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
        $uri = $this->request->path();

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
                $this->response = $this->invoke($route['fn'], $params);
                
                $numHandled++;
                
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        return $numHandled;
    }

    /**
     * Invoke the given function with parameters
     *
     * @param string $fn
     * @param array $params
     * @return Response|mixed
     */
    private function invoke($fn, $params = array())
    {
        // check if its a callback if then call it
        if (is_callable($fn)) {
            $this->response = call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            // laravel like Controller@method routing
            list($controller, $method) = explode('@', $fn);
            $controller = 'App\\Controllers\\'.$controller;
            $this->response = call_user_func_array(array(new $controller(), $method), $params);
        } else {
            throw new \InvalidArgumentException("Only callback or String allowed!");
        }
        return $this->response;
    }
}
