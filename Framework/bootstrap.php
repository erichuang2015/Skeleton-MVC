<?php

// Defining the required constants

define('ROOT', dirname(getcwd()).DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT.'App'.DIRECTORY_SEPARATOR);
define('BASE_PATH', ROOT.'Framework'.DIRECTORY_SEPARATOR);
define('STORAGE_PATH', ROOT.'storage'.DIRECTORY_SEPARATOR);

define('CONTROLLER_PATH', APP_PATH.'Controllers'.DIRECTORY_SEPARATOR);
define('MODEL_PATH', APP_PATH.'Models'.DIRECTORY_SEPARATOR);
define('VIEW_PATH', APP_PATH.'views'.DIRECTORY_SEPARATOR);

/**
 * Application/Database Configuration from user
 */
$config = require(APP_PATH.'config.php');
$db_config = require(APP_PATH.'database.php');

if (!(is_array($config) && is_array($db_config))) {
    throw new Exception('$config and $db_config should be array!');
}

/**
 * Load framework required files
 */
require_once BASE_PATH."autoloader.php";
require_once BASE_PATH."helpers.php";
require_once APP_PATH."routes.php";