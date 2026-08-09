<?php

namespace App\Models;

use App\Core\Database;

/**
 * Repository for the `messages` table: everything sent through the
 * contact form is stored here, whether or not the e-mail went out,
 * so the administrator never loses a message.
 */
class MessageRepository
{
    private $db;
    private $table = 'messages';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll($sort = 'newest')
    {
        $direction = ($sort === 'oldest') ? 'ASC' : 'DESC';

        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY created_at $direction, id $direction")
            ->fetchAll();
    }

    // How many messages the admin has not opened yet (for the red dot).
    public function countUnread()
    {
        return (int) $this->db
            ->query("SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0")
            ->fetchColumn();
    }

    // Called when the admin opens the inbox: nothing is "new" any more.
    public function markAllRead()
    {
        return $this->db->exec("UPDATE {$this->table} SET is_read = 1 WHERE is_read = 0");
    }

    // CREATE: returns the new message id, or false on failure.
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (sender_name, sender_email, subject, body)
                VALUES (:sender_name, :sender_email, :subject, :body)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'sender_name'  => $data['sender_name'],
            'sender_email' => $data['sender_email'],
            'subject'      => $data['subject'],
            'body'         => $data['body'],
        ]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    // Record that the copy of this message really went out over SMTP.
    public function markSent($id)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET email_sent = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}