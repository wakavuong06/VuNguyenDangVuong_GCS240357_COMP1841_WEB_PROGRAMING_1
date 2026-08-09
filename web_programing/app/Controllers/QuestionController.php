<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Upload;
use App\Models\QuestionRepository;
use App\Models\ModuleRepository;
use App\Models\UserRepository;

/*
 * CRUD for questions: show, create, edit and delete.
 */

class QuestionController extends Controller
{
    // READ one question.  URL: /question/show/3
    public function show($id = null)
    {
        $questionRepo = new QuestionRepository();
        $question = $questionRepo->find($id);

        if (!$question) {
            (new HomeController())->notFound();
            return;
        }

        // Only the author or an admin may edit or delete it.
        $canManage = Auth::check()
            && (Auth::id() === (int) $question->user_id || Auth::isAdmin());

        $this->renderView('question/show', [
            'pageTitle' => $question->title,
            'question'  => $question,
            'author'    => $question->user(),
            'module'    => $question->module(),
            'canManage' => $canManage,
        ]);
    }

    // CREATE step 1: show the empty form.  URL: /question/create
    public function create()
    {
        Auth::requireLogin();

        $moduleRepo = new ModuleRepository();

        $this->renderView('question/form', [
            'pageTitle' => 'Ask a question',
            'question'  => null,          // null = we are adding, not editing
            'modules'   => $moduleRepo->getAll(),
            'users'     => $this->authorList(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    // CREATE step 2: check the form and save it.  URL: /question/store
    public function store()
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/question/create');
        }

        $errors = [];
        $data   = $this->readForm($errors);

        // The screenshot is optional. Upload::image() checks it.
        $uploadError = null;
        $imageName = Upload::image(isset($_FILES['image']) ? $_FILES['image'] : null, $uploadError);
        if ($uploadError) {
            $errors['image'] = $uploadError;
        }

        // If anything is wrong, show the form again with the messages
        // and the text the user already typed.
        if ($errors) {
            $moduleRepo = new ModuleRepository();
            $this->renderView('question/form', [
                'pageTitle' => 'Ask a question',
                'question'  => null,
                'modules'   => $moduleRepo->getAll(),
                'users'     => $this->authorList(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        $data['image'] = $imageName;

        $questionRepo = new QuestionRepository();
        $newId = $questionRepo->create($data);

        if ($newId) {
            redirect('/question/show/' . $newId);
        }

        flash('danger', 'Something went wrong while saving. Please try again.');
        redirect('/question/create');
    }

    // UPDATE step 1: show the form filled in.  URL: /question/edit/3
    public function edit($id = null)
    {
        $question = $this->findMyQuestion($id);
        $moduleRepo = new ModuleRepository();

        $this->renderView('question/form', [
            'pageTitle' => 'Edit question',
            'question'  => $question,
            'modules'   => $moduleRepo->getAll(),
            'users'     => $this->authorList(),
            'errors'    => [],
            'old'       => [
                'title'     => $question->title,
                'body'      => $question->body,
                'module_id' => $question->module_id,
                'user_id'   => $question->user_id,
            ],
        ]);
    }

    // UPDATE step 2: save the changes.  URL: /question/update/3
    public function update($id = null)
    {
        $question = $this->findMyQuestion($id);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/question/edit/' . $question->id);
        }

        $errors = [];
        $data   = $this->readForm($errors);

        // Decide what happens to the screenshot: keep it, replace it,
        // or remove it if the checkbox was ticked.
        $imageName = $question->image;

        $uploadError = null;
        $newImage = Upload::image(isset($_FILES['image']) ? $_FILES['image'] : null, $uploadError);
        if ($uploadError) {
            $errors['image'] = $uploadError;
        } elseif ($newImage) {
            Upload::remove($question->image);
            $imageName = $newImage;
        } elseif (!empty($_POST['remove_image'])) {
            Upload::remove($question->image);
            $imageName = null;
        }

        if ($errors) {
            $moduleRepo = new ModuleRepository();
            $this->renderView('question/form', [
                'pageTitle' => 'Edit question',
                'question'  => $question,
                'modules'   => $moduleRepo->getAll(),
                'users'     => $this->authorList(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        $data['image'] = $imageName;

        $questionRepo = new QuestionRepository();
        $questionRepo->update($question->id, $data);

        redirect('/question/show/' . $question->id);
    }

    // DELETE.  URL: /question/delete/3  (POST only)
    public function delete($id = null)
    {
        $question = $this->findMyQuestion($id);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/question/show/' . $question->id);
        }

        Upload::remove($question->image);   // delete the file as well

        $questionRepo = new QuestionRepository();
        $questionRepo->delete($question->id);

        redirect('/');
    }

    /* ---------------- helpers used by the actions above --------------- */
    private function findMyQuestion($id)
    {
        Auth::requireLogin();

        $questionRepo = new QuestionRepository();
        $question = $questionRepo->find($id);

        if (!$question) {
            flash('danger', 'That question no longer exists.');
            redirect('/');
        }

        if (Auth::id() !== (int) $question->user_id && !Auth::isAdmin()) {
            flash('danger', 'You can only change your own questions.');
            redirect('/question/show/' . $question->id);
        }

        return $question;
    }

    private function readForm(&$errors)
    {
        $title    = trim(isset($_POST['title']) ? $_POST['title'] : '');
        $body     = trim(isset($_POST['body']) ? $_POST['body'] : '');
        $moduleId = filter_input(INPUT_POST, 'module_id', FILTER_VALIDATE_INT);

        if ($title === '') {
            $errors['title'] = 'Please give your question a title.';
        } elseif (mb_strlen($title) < 5 || mb_strlen($title) > 150) {
            $errors['title'] = 'The title must be between 5 and 150 characters.';
        }

        if ($body === '') {
            $errors['body'] = 'Please describe your problem.';
        } elseif (mb_strlen($body) < 20) {
            $errors['body'] = 'Please add a little more detail (at least 20 characters).';
        }

        if (!$moduleId) {
            $errors['module_id'] = 'Please choose the module this question belongs to.';
        } elseif (!(new ModuleRepository())->find($moduleId)) {
            $errors['module_id'] = 'That module does not exist.';
        }

        if (Auth::isAdmin()) {
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if (!$userId) {
                $errors['user_id'] = 'Please choose the author of this question.';
            } elseif (!(new UserRepository())->find($userId)) {
                $errors['user_id'] = 'That user does not exist.';
            }
        } else {
            $userId = Auth::id();
        }

        // The text is saved exactly as typed and escaped with e() when it
        // is printed, so "&" does not turn into "&amp;amp;".
        return [
            'title'     => $title,
            'body'      => $body,
            'module_id' => $moduleId,
            'user_id'   => $userId,
        ];
    }

    // The list for the author dropdown; only admins ever see it.
    private function authorList()
    {
        return Auth::isAdmin() ? (new UserRepository())->getAll() : [];
    }
}
