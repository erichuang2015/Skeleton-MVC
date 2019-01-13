<?php

// Defining the required constants
define('ROOT', dirname(getcwd()).DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT.'App'.DIRECTORY_SEPARATOR);
define('BASE_PATH', ROOT.'Skeleton'.DIRECTORY_SEPARATOR);
define('STORAGE_PATH', ROOT.'storage'.DIRECTORY_SEPARATOR);

define('CONTROLLER_PATH', APP_PATH.'Controllers'.DIRECTORY_SEPARATOR);
define('MODEL_PATH', APP_PATH.'Models'.DIRECTORY_SEPARATOR);
define('VIEW_PATH', APP_PATH.'views'.DIRECTORY_SEPARATOR);

/**
 * Application/Database Configuration from user
 */
$config = require_once APP_PATH.'config.php';
$db_config = require_once APP_PATH.'database.php';

if (!(is_array($config) && is_array($db_config))) {
    throw new \InvalidArgumentException('$config and $db_config should be an array!');
}

/**
 * Load framework required files
 */
require_once BASE_PATH."autoloader.php";
require_once BASE_PATH."helpers.php";

/**
 * Request to handle
 */

$request = new Skeleton\Core\Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);

/**
 * Router initialization
 */
$router = new Skeleton\Core\Router($request);
require_once APP_PATH."routes.php";

$response = $router->run();
if ($response instanceof Skeleton\Core\Response) {
    foreach ($response->getHeaders() as $header => $value) {
        header("$header: $value");
    }
    http_response_code($response->getStatus());
    echo $response->getContent();
}
