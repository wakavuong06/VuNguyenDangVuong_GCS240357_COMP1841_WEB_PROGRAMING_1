<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Upload;
use App\Models\UserRepository;

/**
 * Admin area: manage the list of user accounts.
 * Admin-only, so the check runs in the constructor before any action.
 */
class UserController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    // READ: the table of all users. URL: /user
    public function index()
    {
        $userRepo = new UserRepository();

        $this->renderView('user/index', [
            'pageTitle' => 'Manage users',
            'users'     => $userRepo->getAll(),
        ]);
    }

    // CREATE, step 1: the empty form.
    public function create()
    {
        $this->renderView('user/form', [
            'pageTitle' => 'Add a user',
            'user'      => null,          // null = adding, not editing
            'errors'    => [],
            'old'       => [],
        ]);
    }

    // CREATE, step 2: validate and insert. (POST)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/create');
        }

        [$data, $errors] = $this->validateUserInput(0, true);

        if ($errors) {
            $this->renderView('user/form', [
                'pageTitle' => 'Add a user',
                'user'      => null,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        (new UserRepository())->create([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'      => $data['role'],
        ]);

        redirect('/user');
    }

    // UPDATE, step 1: the form filled with the current values.
    public function edit($id = null)
    {
        $user = (new UserRepository())->find((int) $id);

        if (!$user) {
            flash('danger', 'That user does not exist.');
            redirect('/user');
        }

        $this->renderView('user/form', [
            'pageTitle' => 'Edit user',
            'user'      => $user,
            'errors'    => [],
            'old'       => [
                'full_name' => $user->full_name,
                'username'  => $user->username,
                'email'     => $user->email,
                'role'      => $user->role,
            ],
        ]);
    }

    // UPDATE, step 2: validate and save. (POST)
    public function update($id = null)
    {
        $userRepo = new UserRepository();
        $user = $userRepo->find((int) $id);

        if (!$user) {
            flash('danger', 'That user does not exist.');
            redirect('/user');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/edit/' . $user->id);
        }

        // On the edit form the password is optional:
        // leaving it blank keeps the current one.
        $passwordRequired = false;
        [$data, $errors] = $this->validateUserInput($user->id, $passwordRequired);

        if ($errors) {
            $this->renderView('user/form', [
                'pageTitle' => 'Edit user',
                'user'      => $user,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        $userRepo->update($user->id, [
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'role'      => $data['role'],
        ]);

        if ($data['password'] !== '') {
            $userRepo->updatePassword($user->id, password_hash($data['password'], PASSWORD_DEFAULT));
        }

        // If an admin edits their own account, refresh the session copy.
        if (Auth::id() === (int) $user->id) {
            $fresh = $userRepo->find($user->id);
            Auth::login([
                'id'        => $fresh->id,
                'full_name' => $fresh->full_name,
                'username'  => $fresh->username,
                'email'     => $fresh->email,
                'role'      => $fresh->role,
            ]);
        }

        redirect('/user');
    }

    /*
     * DELETE (POST). The foreign key uses ON DELETE CASCADE, so deleting
     * a user also deletes their questions - the confirm dialog warns
     * about that. Their image files are removed first, because MySQL
     * only deletes rows, not files on disk.
     */
    public function delete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user');
        }

        $userRepo = new UserRepository();
        $user = $userRepo->find((int) $id);

        if (!$user) {
            flash('danger', 'That user does not exist.');
            redirect('/user');
        }

        if (Auth::id() === (int) $user->id) {
            flash('danger', 'You cannot delete the account you are logged in with.');
            redirect('/user');
        }

        foreach ($user->questions() as $question) {
            Upload::remove($question['image'] ?? null);
        }

        $userRepo->delete($user->id);

        redirect('/user');
    }

    // Shared server-side validation for store() and update().
    private function validateUserInput($ignoreId, bool $passwordRequired)
    {
        $userRepo = new UserRepository();
        $errors = [];

        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $role     = $_POST['role'] ?? 'member';

        if ($fullName === '' || mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
            $errors['full_name'] = 'Please enter a name (2-100 characters).';
        }

        if (!preg_match('/^[a-zA-Z0-9._]{3,30}$/', $username)) {
            $errors['username'] = 'Usernames are 3-30 characters: letters, numbers, dots or underscores.';
        } elseif ($userRepo->usernameTaken($username, $ignoreId)) {
            $errors['username'] = 'That username is already taken.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid e-mail address.';
        } elseif ($userRepo->emailTaken($email, $ignoreId)) {
            $errors['email'] = 'An account with that e-mail already exists.';
        }

        if ($passwordRequired || $password !== '') {
            if (strlen($password) < 8) {
                $errors['password'] = 'The password must be at least 8 characters long.';
            } elseif ($password !== $confirm) {
                $errors['password_confirm'] = 'The two passwords do not match.';
            }
        }

        if (!in_array($role, ['member', 'admin'], true)) {
            $errors['role'] = 'Please choose a valid role.';
        }

        return [[
            'full_name' => $fullName,
            'username'  => $username,
            'email'     => $email,
            'password'  => $password,
            'role'      => $role,
        ], $errors];
    }
}