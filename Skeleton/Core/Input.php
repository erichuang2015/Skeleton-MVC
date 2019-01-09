<?php

namespace Skeleton\Core;

/**
 * Preprocesses the input that can be used in application then.
 */
final class Input
{
    public function __construct()
    {
        // TODO: set private security instance here
    }

    /**
     * Used to fetch value from superglobals like $\_GET, $\_POST, etc
     *
     * @param array &$array     superglobals like $\_GET, $\_POST
     * @param string $index     index of item to be fetched
     * @param boolean $xssClean whether or not to xss clean output
     * @return mixed
     */
    private function fetchFromArray(&$array, $index = null, $xssClean = false)
    {
        // Determine whether entire array or single index is requested
        isset($index) or $index = array_keys($array);

        if (is_array($index)) {
            $output = array();
            foreach ($index as $key) {
                $output[$key] = $this->fetchFromArray($array, $key, $xssClean);
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

        // TODO: check if xss clean is true then clean and return value
        return $value;
    }

    /**
     * Fetch item from GET superglobal
     *
     * @param mixed $index
     * @param boolean $xssClean
     * @return array|string
     */
    public function get($index = null, $xssClean = false)
    {
        return $this->fetchFromArray($_GET, $index, $xssClean);
    }

    /**
     * Fetch item from POST superglobal
     *
     * @param mixed $index
     * @param boolean $xssClean
     * @return array|string
    */
    public function post($index = null, $xssClean = false)
    {
        return $this->fetchFromArray($_POST, $index, $xssClean);
    }

    /**
     * Fetch item from COOKIE superglobal
     *
     * @param mixed $index
     * @param boolean $xssClean
     * @return array|string
    */
    public function cookie($index = null, $xssClean = false)
    {
        return $this->fetchFromArray($_COOKIE, $index, $xssClean);
    }

    /**
     * Fetch item from SERVER superglobal
     *
     * @param mixed $index
     * @param boolean $xssClean
     * @return array|string
    */
    public function server($index, $xssClean = false)
    {
        return $this->fetchFromArray($_SERVER, $index, $xssClean);
    }

    /**
     * Fetch the value form input field
     *
     * @param mixed $index
     * @param boolean $xssClean
     * @return array|string
    */
    public function input($index = null, $xssClean = false)
    {
        $value = ($_SERVER['REQUEST_METHOD'] == 'GET') ?
            $this->get($index, $xssClean) :
            $this->post($index, $xssClean);
        return $value;
    }
}
