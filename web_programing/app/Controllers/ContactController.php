<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Mailer;
use App\Models\MessageRepository;

/**
 * The public contact form. Every message is saved in the database first,
 * then a copy is e-mailed to the administrator with PHPMailer, so no
 * message is ever lost even when SMTP is unavailable.
 */
class ContactController extends Controller
{
    // The form. Logged-in users get their name and e-mail pre-filled.
    public function index()
    {
        $old = [];
        if (Auth::check()) {
            $old = [
                'sender_name'  => Auth::user()['full_name'],
                'sender_email' => Auth::user()['email'],
            ];
        }

        $this->renderView('contact/index', [
            'pageTitle' => 'Contact the administrator',
            'errors'    => [],
            'old'       => $old,
        ]);
    }

    // Validate, store and e-mail the message. (POST)
    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/contact');
        }

        $errors = [];

        $name    = trim($_POST['sender_name'] ?? '');
        $email   = trim($_POST['sender_email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['body'] ?? '');

        if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            $errors['sender_name'] = 'Please enter your name (2-100 characters).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['sender_email'] = 'Please enter a valid e-mail address so the admin can reply.';
        }
        if ($subject === '' || mb_strlen($subject) < 3 || mb_strlen($subject) > 150) {
            $errors['subject'] = 'Please give the message a subject (3-150 characters).';
        }
        if ($body === '' || mb_strlen($body) < 10) {
            $errors['body'] = 'Please write a few more words (at least 10 characters).';
        }

        $data = [
            'sender_name'  => $name,
            'sender_email' => $email,
            'subject'      => $subject,
            'body'         => $body,
        ];

        if ($errors) {
            $this->renderView('contact/index', [
                'pageTitle' => 'Contact the administrator',
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        // 1. Keep a permanent copy in the database.
        $messageRepo = new MessageRepository();
        $messageId = $messageRepo->create($data);

        // 2. Try to e-mail it to the administrator with PHPMailer.
        $sent = Mailer::send(
            ADMIN_EMAIL,
            'Site administrator',
            '[' . SITE_NAME . ' contact] ' . $subject,
            '<p><strong>From:</strong> ' . e($name) . ' &lt;' . e($email) . '&gt;</p>'
            . '<p>' . nl2br(e($body)) . '</p>'
        );

        if ($sent && $messageId) {
            $messageRepo->markSent($messageId);
        }

        redirect('/contact');
    }

    // Admin-only inbox showing every message ever received.
    public function inbox()
    {
        Auth::requireAdmin();

        // ?sort=oldest shows the earliest message first; the default is newest.
        $sort = (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'oldest' : 'newest';

        $messageRepo = new MessageRepository();
        $messages = $messageRepo->getAll($sort);

        // Opening the inbox means the admin has seen everything, so the
        // red dot on the Inbox link disappears from the next page on.
        $messageRepo->markAllRead();

        $this->renderView('contact/inbox', [
            'pageTitle' => 'Contact inbox',
            'messages'  => $messages,
            'sort'      => $sort,
        ]);
    }
}