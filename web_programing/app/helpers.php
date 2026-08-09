<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Save a one-off message in the session (shown on the next page).
function flash($type, $text)
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

// Read the flash message and delete it, so it only appears once.
function get_flash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

// Send the browser to another page of this site and stop.
function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit();
}

// Turn "2026-07-18 14:05:00" into "18 Jul 2026, 14:05".
function nice_date($mysqlDate)
{
    return $mysqlDate ? date('j M Y, H:i', strtotime($mysqlDate)) : '';
}

// Shorten long text for the question list.
function excerpt($text, $limit = 150)
{
    $text = trim($text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '...';
}