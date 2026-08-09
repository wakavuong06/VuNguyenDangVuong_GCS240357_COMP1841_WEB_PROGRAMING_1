<?php

namespace App\Models;
use App\Core\Database;
use PDO;

class QuestionRepository
{
    private $db;
    private $table = 'questions';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function search($keyword, $moduleId, $limit, $offset)
    {
        $sql = "SELECT q.*, u.full_name AS author_name, m.code AS module_code, m.name AS module_name
                FROM questions q
                JOIN users u   ON u.id = q.user_id
                JOIN modules m ON m.id = q.module_id
                WHERE 1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (q.title LIKE :keyword OR q.body LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($moduleId) {
            $sql .= " AND q.module_id = :module_id";
            $params['module_id'] = $moduleId;
        }

        // LIMIT and OFFSET are cast to integers, so they are safe to insert.
        $sql .= " ORDER BY q.created_at DESC LIMIT " . (int) $limit . " OFFSET " . (int) $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // How many questions match the same filters (used for the page count).
    public function countSearch($keyword, $moduleId)
    {
        $sql = "SELECT COUNT(*) FROM questions q WHERE 1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (q.title LIKE :keyword OR q.body LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        if ($moduleId) {
            $sql .= " AND q.module_id = :module_id";
            $params['module_id'] = $moduleId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // Find one question by id, or null if it does not exist.
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Question($data) : null;
    }

    // CREATE. Returns the new id so the controller can redirect to it.
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, module_id, title, body, image)
                VALUES (:user_id, :module_id, :title, :body, :image)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'user_id'   => $data['user_id'],
            'module_id' => $data['module_id'],
            'title'     => $data['title'],
            'body'      => $data['body'],
            'image'     => $data['image'],
        ]);
        return $ok ? $this->db->lastInsertId() : false;
    }

    // UPDATE
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET title = :title, body = :body, module_id = :module_id,
                    user_id = :user_id, image = :image
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'        => $id,
            'title'     => $data['title'],
            'body'      => $data['body'],
            'module_id' => $data['module_id'],
            'user_id'   => $data['user_id'],
            'image'     => $data['image'],
        ]);
    }

    // DELETE
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Every image file belonging to one user (used before deleting them).
    public function imagesOfUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT image FROM {$this->table} WHERE user_id = :id AND image IS NOT NULL"
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}