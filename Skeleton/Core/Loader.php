<?php

class Loader
{

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
        $viewLoader = new View(VIEW_PATH);
        return response(
            $viewLoader->view($view, $data),
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
        return response(
            \json_encode($data),
            $status,
            $headers
        )->header("Content-Type", "application/json");
    }
}
