<?php

namespace Skeleton\Core;

/**
 * Preprocesses the input that can be used in application then.
 */
class Input
{
    /**
     * @var array
     */
    private $serverParams = [];
    
    /**
     * @var array
     */
    private $getParams = [];
    
    /**
     * @var array
     */
    private $postParams = [];
    
    /**
     * @var array
     */
    private $filesParams = [];
    
    /**
     * @var array
     */
    private $cookieParams = [];

    public function __construct($server = [], $get = [], $post = [], $files = [], $cookie = [])
    {
        $this->serverParams = $server;
        $this->getParams = $get;
        $this->postParams = $post;
        $this->filesParams = $files;
        $this->cookieParams = $cookie;
    }

    /**
     * Used to fetch value from superglobals like $\_GET, $\_POST, etc
     *
     * @param array &$array     superglobals like $\_GET, $\_POST
     * @param string $index     index of item to be fetched
     * @return mixed
     */
    private function fetchFromArray(&$array, $index = null)
    {
        // Determine whether entire array or single index is requested
        isset($index) or $index = array_keys($array);

        if (is_array($index)) {
            $output = array();
            foreach ($index as $key) {
                $output[$key] = $this->fetchFromArray($array, $key);
            }

            return $output;
        }

        if (isset($array[$index])) {
            $value = $array[$index];
        } elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) {
            $value = $array;
            for ($i=0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');

                if ($key === '') {
                    break;
                }

                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }

        return $value;
    }

    /**
     * Fetch item from GET superglobal
     *
     * @param mixed $index
     * @return array|string
     */
    public function get($index = null)
    {
        return $this->fetchFromArray($this->getParams, $index);
    }

    /**
     * Fetch item from POST superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function post($index = null)
    {
        return $this->fetchFromArray($this->postParams, $index);
    }

    /**
     * Fetch item from COOKIE superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function cookie($index = null)
    {
        return $this->fetchFromArray($this->cookieParams, $index);
    }

    /**
     * Fetch item from SERVER superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function server($index = null)
    {
        return $this->fetchFromArray($this->serverParams, $index);
    }

    public function __call($name, $arguments)
    {
        throw new \BadMethodCallException("Undefined method called $name", 1);
    }
}
