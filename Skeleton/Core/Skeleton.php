<?php

namespace Skeleton\Core;

class Skeleton
{
    /** @var string */
    private $rootPath;
    
    /** @var string */
    private $appPath;

    /** @var string */
    private $basePath;

    /** @var string */
    private $storagePath;

    /** @var string */
    private $controllerPath;

    /** @var string */
    private $modelPath;

    /** @var string */
    private $viewPath;

    /** @var array */
    private $config;

    /** @var array */
    private $db_config;

    /** @var Request */
    private $request = null;

    /** @var Response */
    private $response = null;

    /** @var Router */
    private $router = null;

    /** @var Loader */
    private $loader = null;

    /** @var Skeleton */
    private static $skeleton;

    /**
     * To get instance properties
     *
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        // if (\strpos($name, 'Path') === false) {
        //     throw new \RuntimeException("Undefined property $name");
        // }
        return $this->$name;
    }

    protected function __construct()
    {
        $this->declarePaths();
        $this->loadConfig();
        $this->runApp();
    }

    /**
     * Get Singleton Instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$skeleton)) {
            self::$skeleton = new Skeleton();
        }

        return self::$skeleton;
    }

    /**
     * Declare application required paths
     *
     * @return void
     */
    private function declarePaths()
    {
        $this->rootPath = \dirname(\getcwd()).DIRECTORY_SEPARATOR;

        $this->appPath = $this->rootPath.'App'.DIRECTORY_SEPARATOR;
        $this->basePath = $this->rootPath.'Skeleton'.DIRECTORY_SEPARATOR;
        $this->storagePath = $this->rootPath.'storage'.DIRECTORY_SEPARATOR;

        $this->controllerPath = $this->appPath.'Controllers'.DIRECTORY_SEPARATOR;
        $this->modelPath = $this->appPath.'Models'.DIRECTORY_SEPARATOR;
        $this->viewPath = $this->appPath.'views'.DIRECTORY_SEPARATOR;
    }

    /**
     * Load configuration from user directories
     *
     * @return void
     */
    private function loadConfig()
    {
        $this->config = require $this->appPath.'config.php';
        $this->db_config = require $this->appPath.'database.php';
        if (!(is_array($this->config) && is_array($this->db_config))) {
            throw new \InvalidArgumentException('$config and $db_config should be an array!');
        }
    }

    /**
     * Method starts and completes the Request-Response cycle
     *
     * @return void
     */
    private function runApp()
    {
        $this->loader = new Loader($this->viewPath);
        $this->request = $this->listenToRequest();
        $this->response = $this->handleRequest();
        $this->sendResponse();
    }

    /**
     * Listen to the Request
     *
     * @return Request
     */
    private function listenToRequest()
    {
        return $this->request ?: new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }

    /**
     * Handles the requests and gives reponse
     *
     * @return Response|mixed
     */
    private function handleRequest()
    {
        $this->router = new Router($this->request);
        require_once $this->appPath.'routes.php';
        return $this->router->run();
    }

    /**
     * Send Response to client
     *
     * @return void
     */
    private function sendResponse()
    {
        if ($this->response instanceof Response) {
            foreach ($this->response->getHeaders() as $header => $value) {
                header("$header: $value");
            }
            http_response_code($this->response->getStatus());
            echo $this->response->getContent();
        }
    }
}
