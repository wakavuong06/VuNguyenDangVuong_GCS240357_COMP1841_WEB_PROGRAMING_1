<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ModuleRepository;
use PDOException;

class ModuleController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    // READ: the table of all modules. URL: /module
    public function index()
    {
        $moduleRepo = new ModuleRepository();

        $this->renderView('module/index', [
            'pageTitle' => 'Manage modules',
            'modules'   => $moduleRepo->getAll(),
        ]);
    }

    // CREATE, step 1: the empty form.
    public function create()
    {
        $this->renderView('module/form', [
            'pageTitle' => 'Add a module',
            'module'    => null,          // null = adding, not editing
            'errors'    => [],
            'old'       => [],
        ]);
    }

    // CREATE, step 2: validate and insert. (POST)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/module/create');
        }

        [$data, $errors] = $this->validateModuleInput();

        if ($errors) {
            $this->renderView('module/form', [
                'pageTitle' => 'Add a module',
                'module'    => null,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        (new ModuleRepository())->create($data);

        redirect('/module');
    }

    // UPDATE, step 1: the form filled with the current values.
    public function edit($id = null)
    {
        $module = (new ModuleRepository())->find((int) $id);

        if (!$module) {
            flash('danger', 'That module does not exist.');
            redirect('/module');
        }

        $this->renderView('module/form', [
            'pageTitle' => 'Edit module',
            'module'    => $module,
            'errors'    => [],
            'old'       => [
                'code'        => $module->code,
                'name'        => $module->name,
                'description' => $module->description,
            ],
        ]);
    }

    // UPDATE, step 2: validate and save. (POST)
    public function update($id = null)
    {
        $moduleRepo = new ModuleRepository();
        $module = $moduleRepo->find((int) $id);

        if (!$module) {
            flash('danger', 'That module does not exist.');
            redirect('/module');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/module/edit/' . $module->id);
        }

        [$data, $errors] = $this->validateModuleInput($module->id);

        if ($errors) {
            $this->renderView('module/form', [
                'pageTitle' => 'Edit module',
                'module'    => $module,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        $moduleRepo->update($module->id, $data);

        redirect('/module');
    }

    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/module');
        }

        try {
            (new ModuleRepository())->delete((int) $id);
        } catch (PDOException $e) {
            flash('danger', 'This module still has questions assigned to it. '
                . 'Move or delete those questions first, then try again.');
        }

        redirect('/module');
    }

    // Shared server-side validation for store() and update().
    private function validateModuleInput($ignoreId = 0)
    {
        $errors = [];

        $code        = strtoupper(trim($_POST['code'] ?? ''));
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!preg_match('/^[A-Z0-9]{4,10}$/', $code)) {
            $errors['code'] = 'Codes are 4-10 letters/numbers, e.g. COMP1841.';
        } elseif ((new ModuleRepository())->codeTaken($code, $ignoreId)) {
            $errors['code'] = 'A module with that code already exists.';
        }

        if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 100) {
            $errors['name'] = 'Please enter the module name (3-100 characters).';
        }

        if (mb_strlen($description) > 255) {
            $errors['description'] = 'The description can be at most 255 characters.';
        }

        return [[
            'code'        => $code,
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
        ], $errors];
    }
}