<?php

namespace App\Models;
use App\Core\Database;
use PDO;

class Question
{
    private $db;

    public $id;
    public $user_id;
    public $module_id;
    public $title;
    public $body;
    public $image;
    public $created_at;

    public function __construct($data = [])
    {
        $database = new Database();
        $this->db = $database->connect();

        $this->id         = isset($data['id']) ? $data['id'] : null;
        $this->user_id    = isset($data['user_id']) ? $data['user_id'] : null;
        $this->module_id  = isset($data['module_id']) ? $data['module_id'] : null;
        $this->title      = isset($data['title']) ? $data['title'] : null;
        $this->body       = isset($data['body']) ? $data['body'] : null;
        $this->image      = isset($data['image']) ? $data['image'] : null;
        $this->created_at = isset($data['created_at']) ? $data['created_at'] : null;
    }

    // The student who wrote this question.
    public function user()
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $this->user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    // The module this question belongs to.
    public function module()
    {
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = :module_id");
        $stmt->execute(['module_id' => $this->module_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Module($data) : null;
    }
}