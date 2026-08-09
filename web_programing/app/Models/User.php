<?php

namespace App\Models;
use App\Core\Database;
use PDO;

class User
{
    private $db;

    public $id;
    public $full_name;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;

    public function __construct($data = [])
    {
        $database = new Database();
        $this->db = $database->connect();

        $this->id         = isset($data['id']) ? $data['id'] : null;
        $this->full_name  = isset($data['full_name']) ? $data['full_name'] : null;
        $this->username   = isset($data['username']) ? $data['username'] : null;
        $this->email      = isset($data['email']) ? $data['email'] : null;
        $this->password   = isset($data['password']) ? $data['password'] : null;
        $this->role       = isset($data['role']) ? $data['role'] : null;
        $this->created_at = isset($data['created_at']) ? $data['created_at'] : null;
    }

    // All questions written by this user.
    public function questions()
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM questions WHERE user_id = :id ORDER BY created_at DESC"
        );
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}