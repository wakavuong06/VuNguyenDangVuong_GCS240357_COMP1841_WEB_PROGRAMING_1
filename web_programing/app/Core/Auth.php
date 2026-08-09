<?php

namespace App\Core;

/*
 * Auth keeps track of who is logged in. The details are stored in the
 * session, so every page can ask "who is this?" in one line.
 */
class Auth
{
    // Is anybody logged in?
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    // The logged-in user's details, or null for a guest.
    public static function user()
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    // The logged-in user's id, or null for a guest.
    public static function id()
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    // True only for accounts with the admin role.
    public static function isAdmin()
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    // Called after a correct password. Stores the user in the session.
    public static function login($user)
    {
        // A new session id on login stops "session fixation" attacks.
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'full_name' => $user['full_name'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
        ];
    }

    // Log out.
    public static function logout()
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    // Guests are sent to the login page.
    public static function requireLogin()
    {
        if (!self::check()) {
            flash('warning', 'Please log in to continue.');
            redirect('/auth/login');
        }
    }

    // Only admins may continue.
    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            flash('danger', 'That area is for administrators only.');
            redirect('/');
        }
    }
}
