<?php

namespace Skeleton\Core;

/**
 * View class helps to render, loacte, etc. views
 */
final class View
{
    /**
     * root directory of view files
     *
     * @var string
     */
    private $viewPath;

    /**
     *
     * @param string $viewPath root directory of view files
     */
    public function __construct($viewPath = '')
    {
        $this->viewPath = $viewPath;
    }

    /**
     * view function returns all contents of specified view and make passed data available
     * in the symbol table
     * Note: This function will perform htmlspecialchars on data to avoid xss.
     * See Documentation to reverse the effect
     *
     * @param array|string $views single or multiple views
     * @param array $data data to be passed
     * @return void
     */
    public function view($views, $data = array())
    {
        // xss clean data
        array_walk_recursive($data, 'hsc');
        extract($data);

        ob_start();
        foreach ((array)$views as $view) {
            $view = $this->viewPath.str_replace('.', DIRECTORY_SEPARATOR, $view).'.php';
            if (file_exists($view)) {
                include($view);
            } else {
                ob_end_clean();
                throw new \RuntimeException("can't find view $view");
            }
        }
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}
