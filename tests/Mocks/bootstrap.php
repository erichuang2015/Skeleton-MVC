<?php

define('VIEW_PATH', getcwd().DIRECTORY_SEPARATOR.'Mocks/views');

spl_autoload_register('autoload');
function autoload($class)
{
    $class = str_replace("\\", "/", $class);
    $path = getcwd().DIRECTORY_SEPARATOR.$class.'.php';
    if (file_exists($path)) {
        require_once $path;
    }
}
