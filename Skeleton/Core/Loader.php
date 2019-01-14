<?php

namespace Skeleton\Core;

class Loader
{
    private $viewPath;

    public function __construct($viewPath)
    {
        $this->viewPath = $viewPath;
    }
    /**
     * Create a Http Response
     *
     * @param string $content
     * @param integer $status
     * @return Response
     */
    public function response($content = '', $status = 200)
    {
        return new Response($content, $status);
    }

    /**
     * Creates view/views as response
     *
     * @param string|array $view
     * @param array $data
     * @param integer $status
     * @param array $headers
     * @return Response
     */
    public function view($view, $data = [], $status = 200, $headers = [])
    {
        extract(hsa($data));

        ob_start();
        $view = $this->viewPath.str_replace('.', DIRECTORY_SEPARATOR, $view).'.php';
        if (file_exists($view)) {
            include($view);
        } else {
            ob_end_clean();
            throw new \RuntimeException("can't find view $view");
        }
        $content = ob_get_contents();
        ob_end_clean();
        
        return $this->response(
            $content,
            $status,
            $headers
        )->header("Content-Type", "text/html");
    }

    /**
     * Creates Json response with specified hedaers
     *
     * @param array|string $data
     * @param integer $status
     * @param array $headers
     * @return Reponse
     */
    public function json($data = [], $status = 200, $headers = [])
    {
        return $this->response(
            \json_encode($data),
            $status,
            $headers
        )->header("Content-Type", "application/json");
    }
}
