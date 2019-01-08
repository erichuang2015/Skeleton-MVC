<?php

/**
 * Autoload function is used by default
 * you can change it in App/config.php
 */

if ($config['use_composer_autoload'] === false) {
    function autoload($class)
    {
        $class = str_replace("\\", "/", $class);
        if (file_exists(ROOT.$class.'.php')) {
            require_once ROOT.$class.'.php';
        }
    }

    spl_autoload_register('autoload');
}
