<?php

namespace App\Models;

use App\Core\Database;

    // Entity class: one row of the `modules` table as an object.
class Module
{
    private $db;

    public $id;
    public $code;
    public $name;
    public $description;
    public $created_at;

    public function __construct($data = [])
    {
        $database = new Database();
        $this->db = $database->connect();

        $this->id          = $data['id'] ?? null;
        $this->code        = $data['code'] ?? null;
        $this->name        = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->created_at  = $data['created_at'] ?? null;
    }

    // All questions assigned to this module (one-to-many relationship).
    public function questions()
    {
        $sql = "SELECT q.*, u.username
                FROM questions q
                JOIN users u ON u.id = q.user_id
                WHERE q.module_id = :module_id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['module_id' => $this->id]);
        return $stmt->fetchAll();
    }
}
