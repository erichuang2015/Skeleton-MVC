<?php

// Defining the required constants

define('ROOT', getcwd().DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT.'App'.DIRECTORY_SEPARATOR);
define('BASE_PATH', ROOT.'Skeleton'.DIRECTORY_SEPARATOR);
define('STORAGE_PATH', ROOT.'storage'.DIRECTORY_SEPARATOR);

define('CONTROLLER_PATH', APP_PATH.'Controllers'.DIRECTORY_SEPARATOR);
define('MODEL_PATH', APP_PATH.'Models'.DIRECTORY_SEPARATOR);
define('VIEW_PATH', APP_PATH.'views'.DIRECTORY_SEPARATOR);

/**
 * Application/Database Configuration from user
 */
$config = require(APP_PATH.'config.php');
$db_config = require(APP_PATH.'database.php');

/**
 * Load framework required files
 */
require_once BASE_PATH."autoloader.php";
require_once BASE_PATH."helpers.php";
/**
 * Router initialization
 */
// $router = new Framework\Core\Router();
// require_once APP_PATH."routes.php";
// $router->handle();
