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
}
