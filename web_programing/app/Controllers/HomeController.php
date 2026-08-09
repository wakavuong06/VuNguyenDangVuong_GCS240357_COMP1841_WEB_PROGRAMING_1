<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\QuestionRepository;
use App\Models\ModuleRepository;

class HomeController extends Controller
{
    /*
     * The public home page: a list of questions, with an optional
     * keyword search, an optional module filter, and pagination.
     */
    public function index()
    {
        // Read the filters from the URL, e.g. /?q=pdo&module=1&page=2
        $keyword  = trim(isset($_GET['q']) ? $_GET['q'] : '');
        $moduleId = filter_input(INPUT_GET, 'module', FILTER_VALIDATE_INT);
        $page     = max(1, (int) (isset($_GET['page']) ? $_GET['page'] : 1));

        $perPage = 6;
        $offset  = ($page - 1) * $perPage;

        $questionRepo = new QuestionRepository();
        $moduleRepo   = new ModuleRepository();

        $total     = $questionRepo->countSearch($keyword, $moduleId);
        $questions = $questionRepo->search($keyword, $moduleId, $perPage, $offset);
        $modules   = $moduleRepo->getAll();

        $this->renderView('home/index', [
            'pageTitle'  => 'Questions',
            'questions'  => $questions,
            'modules'    => $modules,
            'keyword'    => $keyword,
            'moduleId'   => $moduleId,
            'page'       => $page,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
            'total'      => $total,
        ]);
    }

    // The 404 page. The router calls this for an unknown address.
    public function notFound()
    {
        http_response_code(404);
        $this->renderView('errors/404', ['pageTitle' => 'Page not found']);
    }
}
