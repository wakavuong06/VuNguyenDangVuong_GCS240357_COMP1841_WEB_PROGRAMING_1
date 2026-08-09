<?php

namespace App\Models;

use App\Core\Database;

/**
 * Repository class: all SQL for the `users` table lives here.
 * Every statement is a PDO prepared statement.
 */
class UserRepository
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // All users, plus how many questions each one has written.
    public function getAll()
    {
        $sql = "SELECT u.*,
                       (SELECT COUNT(*) FROM questions q WHERE q.user_id = u.id) AS question_count
                FROM {$this->table} u
                ORDER BY u.id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    // Find one user by id and return it as a User object.
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }


    // Is this e-mail already used by a DIFFERENT account?
    public function emailTaken($email, $ignoreId = 0): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email AND id <> :ignore";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'ignore' => (int) $ignoreId]);
        return (bool) $stmt->fetch();
    }

    // Is this username already used by a DIFFERENT account?
    public function usernameTaken($username, $ignoreId = 0): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE username = :username AND id <> :ignore";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username, 'ignore' => (int) $ignoreId]);
        return (bool) $stmt->fetch();
    }

    // CREATE: returns the new user's id, or false when the insert failed.
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (full_name, username, email, password, role)
                VALUES (:full_name, :username, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => $data['password'],   // already hashed by the controller
            'role'      => $data['role'],
        ]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    // UPDATE the profile fields (the password has its own method below).
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET full_name = :full_name, username = :username,
                    email = :email, role = :role
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'        => $id,
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'role'      => $data['role'],
        ]);
    }

    public function updatePassword($id, $hashedPassword)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE id = :id");
        return $stmt->execute(['id' => $id, 'password' => $hashedPassword]);
    }



    // DELETE: the questions FK is ON DELETE CASCADE, so their rows go too.
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
