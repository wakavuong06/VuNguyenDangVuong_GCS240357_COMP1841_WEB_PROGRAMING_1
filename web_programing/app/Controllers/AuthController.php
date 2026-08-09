<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Mailer;
use App\Models\UserRepository;

class AuthController extends Controller
{
    // ------------------------------------------------------------------

    public function register()
    {
        if (Auth::check()) {
            redirect('/');
        }

        $this->renderView('auth/register', [
            'pageTitle' => 'Create an account',
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function registerPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/auth/register');
        }

        $userRepo = new UserRepository();
        $errors = [];

        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // --- server-side validation (HTML5 covers the client side) ---
        if ($fullName === '' || mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
            $errors['full_name'] = 'Please enter your name (2-100 characters).';
        }

        if (!preg_match('/^[a-zA-Z0-9._]{3,30}$/', $username)) {
            $errors['username'] = 'Usernames are 3-30 characters: letters, numbers, dots or underscores.';
        } elseif ($userRepo->usernameTaken($username)) {
            $errors['username'] = 'That username is already taken.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid e-mail address.';
        } elseif ($userRepo->emailTaken($email)) {
            $errors['email'] = 'An account with that e-mail already exists.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'The password must be at least 8 characters long.';
        } elseif ($password !== $confirm) {
            $errors['password_confirm'] = 'The two passwords do not match.';
        }

        if ($errors) {
            $this->renderView('auth/register', [
                'pageTitle' => 'Create an account',
                'errors'    => $errors,
                'old'       => ['full_name' => $fullName, 'username' => $username, 'email' => $email],
            ]);
            return;
        }

        $userRepo->create([
            'full_name' => $fullName,
            'username'  => $username,
            'email'     => $email,
            // password_hash() uses bcrypt: the real password is never stored.
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role'      => 'member',
        ]);

        // A small welcome mail; if SMTP is not configured it simply
        // is skipped (see App\Core\Mailer).
        Mailer::send(
            $email,
            $fullName,
            'Welcome to ' . SITE_NAME,
            '<p>Hi ' . e($fullName) . ',</p>'
            . '<p>Your ' . SITE_NAME . ' account (<strong>' . e($username) . '</strong>) is ready. '
            . 'Log in and ask your first question!</p>'
        );

        redirect('/auth/login');
    }

    // ------------------------------------------------------------------

    public function login()
    {
        if (Auth::check()) {
            redirect('/');
        }

        $this->renderView('auth/login', [
            'pageTitle' => 'Log in',
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function loginPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/auth/login');
        }

        $identifier = trim($_POST['identifier'] ?? '');   // e-mail OR username
        $password   = $_POST['password'] ?? '';

        $userRepo = new UserRepository();
        $user = str_contains($identifier, '@')
            ? $userRepo->findByEmail($identifier)
            : $userRepo->findByUsername($identifier);

        // One vague message for both "no such user" and "wrong password",
        // so the form cannot be used to discover which e-mails exist.
        if (!$user || !password_verify($password, $user->password)) {
            $this->renderView('auth/login', [
                'pageTitle' => 'Log in',
                'errors'    => ['identifier' => 'The details you entered do not match any account.'],
                'old'       => ['identifier' => $identifier],
            ]);
            return;
        }

        Auth::login([
            'id'        => $user->id,
            'full_name' => $user->full_name,
            'username'  => $user->username,
            'email'     => $user->email,
            'role'      => $user->role,
        ]);

        redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        redirect('/');
    }
}
