<?php

namespace App\Core;

class Controller
{
    /**
     * Render a view file wrapped inside the shared header and footer.
     * The keys of $data become plain variables inside the view.
     */
    protected function renderView($viewPath, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }

        $mainViewFile = ROOT_PATH . '/app/Views/' . $viewPath . '.php';

        if (file_exists($mainViewFile)) {
            require ROOT_PATH . '/app/Views/partials/header.php';
            require $mainViewFile;
            require ROOT_PATH . '/app/Views/partials/footer.php';
        } else {
            die('View file not found: ' . $mainViewFile);
        }
    }
}
