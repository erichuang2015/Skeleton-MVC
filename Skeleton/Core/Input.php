<?php

namespace Skeleton\Core;

/**
 * Preprocesses the input that can be used in application then.
 */
class Input
{

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
        return $this->fetchFromArray($_GET, $index);
    }

    /**
     * Fetch item from POST superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function post($index = null)
    {
        return $this->fetchFromArray($_POST, $index);
    }

    /**
     * Fetch item from COOKIE superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function cookie($index = null)
    {
        return $this->fetchFromArray($_COOKIE, $index);
    }

    /**
     * Fetch item from SERVER superglobal
     *
     * @param mixed $index
     * @return array|string
    */
    public function server($index)
    {
        return $this->fetchFromArray($_SERVER, $index);
    }

    /**
     * Fetch the value form input field
     *
     * @param mixed $index
     * @return array|string
    */
    public function input($index = null)
    {
        $value = ($_SERVER['REQUEST_METHOD'] == 'GET') ?
            $this->get($index, $xssClean) :
            $this->post($index, $xssClean);
        return $value;
    }
}
