<?php

/**
 * This file contains several global functions that can be used any where
 */

/**
 * fake the functionability of getallheaders if not available
 */
if (!function_exists('getallheaders')) {
    // TODO:: Call Request::headers
}

/**
 * Helper function for array_walk_recursive to perform htmlspecialchars
 * Note: parameter is passed by refrence
 *
 * @param string $value
 * @return void
 */
function hsc(&$value, $key)
{
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Deocde the htmlspecialchars encoded values
 * NOTE: Directly outputing value of this function can result Xss attacks
 *
 * @param string $value
 * @return string
 */
function hsd($value)
{
    return htmlspecialchars_decode($value, ENT_QUOTES);
}

/**
 * Xss clean data in array recursively
 *
 * @param array $value
 * @return array
 */
function hsa($value)
{
    $clean_data = $value;
    array_walk_recursive($clean_data, 'hsc');

    return $clean_data;
}

if (!function_exists('response')) {
    /**
     * Create a Http Response
     *
     * @param string $content
     * @param integer $status
     * @return \Skeleton\Core\Response
     */
    function response($content = '', $status = 200)
    {
        return new \Skeleton\Core\Response($content, $status);
    }
}

if (!function_exists('view')) {
    /**
     * Creates view/views as response
     *
     * @param string|array $view
     * @param array $data
     * @param integer $status
     * @param array $headers
     * @return \Skeleton\Core\Response
     */
    function view($view, $data = [], $status = 200, $headers = [])
    {
        $viewLoader = new \Skeleton\Core\View(VIEW_PATH);
        return response(
            $viewLoader->view($view, $data),
            $status,
            $headers
        )->header("Content-Type", "text/html");
    }
}

if (!function_exists('json')) {
    /**
     * Creates Json response with specified hedaers
     *
     * @param array|string $data
     * @param integer $status
     * @param array $headers
     * @return \Skeleton\Core\Reponse
     */
    function json($data = [], $status = 200, $headers = [])
    {
        $viewParser = new \Skeleton\Core\View(VIEW_PATH);
        return response(
            \json_encode($data),
            $status,
            $headers
        )->header("Content-Type", "application/json");
    }
}
