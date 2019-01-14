<?php

require_once '../Skeleton/Core/Skeleton.php';
require_once '../Skeleton/helpers.php';

use Skeleton\Core\Skeleton;

spl_autoload_register('autoload');
function autoload($class)
{
    $class = str_replace("\\", "/", $class);
    $path = dirname(getcwd()).DIRECTORY_SEPARATOR.$class.'.php';
    if (file_exists($path)) {
        require_once $path;
    }
}
$app = Skeleton::getInstance();
