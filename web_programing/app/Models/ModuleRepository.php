<?php

namespace App\Models;

use App\Core\Database;

    // Repository class: all SQL for the `modules` table.
class ModuleRepository
{
    private $db;
    private $table = 'modules';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // All modules with a count of the questions assigned to each.
    public function getAll()
    {
        $sql = "SELECT m.*,
                       (SELECT COUNT(*) FROM questions q WHERE q.module_id = m.id) AS question_count
                FROM {$this->table} m
                ORDER BY m.code ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? new Module($data) : null;
    }

    // Is this module code already used by a DIFFERENT module?
    public function codeTaken($code, $ignoreId = 0): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE code = :code AND id <> :ignore";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code, 'ignore' => (int) $ignoreId]);
        return (bool) $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (code, name, description)
                VALUES (:code, :name, :description)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'],
        ]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET code = :code, name = :name, description = :description
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'          => $id,
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'],
        ]);
    }

    /**
     * DELETE. The questions FK uses ON DELETE RESTRICT, so MySQL refuses
     * to remove a module while questions still point at it. The controller
     * catches that exception and shows a friendly message instead of
     * silently destroying student posts.
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
