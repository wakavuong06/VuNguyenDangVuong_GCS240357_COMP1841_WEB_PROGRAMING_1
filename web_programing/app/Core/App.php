<?php

namespace App\Core;

class App
{
    // Defaults used when the URL is empty, i.e. the home page.
    protected $controller = 'HomeController';
    protected $action = 'index';
    protected $params = [];

    public function __construct()
    {
        // 1. Split the URL into [controller, action, param1, ...]
        $url = $this->parseUrl();

        // 2. Resolve the controller from the first URL segment.
        if (isset($url[0]) && $url[0] !== '') {
            $candidate = ucfirst(strtolower($url[0])) . 'Controller';
            if (file_exists(ROOT_PATH . '/app/Controllers/' . $candidate . '.php')) {
                $this->controller = $candidate;
                unset($url[0]);
            } else {
                $this->showNotFound();   // unknown controller
            }
        }

        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        $this->controller = new $controllerClass();

        // 3. Resolve the action from the second URL segment.
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            } else {
                $this->showNotFound();   // unknown action
            }
        }

        // 4. Whatever is left over becomes the parameters (usually an id).
        $this->params = $url ? array_values($url) : [];

        // 5. Run: $controller->$action($param1, $param2, ...)
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    // Clean the raw url value and split it on "/".
    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    // Show the styled 404 page instead of a blank die().
    private function showNotFound()
    {
        $home = new \App\Controllers\HomeController();
        $home->notFound();
        exit();
    }
}