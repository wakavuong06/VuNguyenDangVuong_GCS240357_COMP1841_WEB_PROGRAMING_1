<?php

use App\Core\Auth;
use App\Models\MessageRepository;

// Which section of the site is open? Used to highlight the active nav link.
$currentSection = explode('/', trim($_GET['url'] ?? '', '/'))[0] ?? '';
$isSection = function (array $sections) use ($currentSection) {
    return in_array($currentSection, $sections, true);
};

// How many contact messages the admin has not opened yet. Only admins
// see the Inbox link, so only they need the number.
$unreadMessages = Auth::isAdmin() ? (new MessageRepository())->countUnread() : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? SITE_NAME) ?> · <?= e(SITE_NAME) ?></title>
    <meta name="description"
        content="StudyGrove is a small question-and-answer board where students help each other with coursework.">

    <link rel="icon" type="image/svg+xml" href="<?= PUBLIC_FOLDER ?>/img/favicon.svg">

    <!-- Fonts: Fraunces for headings, Nunito Sans for everything else.
         If there is no internet connection the CSS falls back to Georgia / system fonts. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Nunito+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">

    <!-- CSS FRAMEWORK: Bootstrap 5.3.3, delivered from the jsDelivr CDN. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    <!-- Our own theme layer. It is loaded AFTER Bootstrap so that it can
         re-declare Bootstrap's --bs-* variables in the StudyGrove colours.
         ?v=... is the file's last-modified time, which forces the browser
         to download the new version instead of reusing a cached one. -->
    <link rel="stylesheet"
        href="<?= PUBLIC_FOLDER ?>/css/style.css?v=<?= filemtime(ROOT_PATH . '/public/css/style.css') ?>">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Keyboard users can jump straight past the navigation (accessibility). -->
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <header class="site-header">
        <nav class="navbar navbar-expand-lg" aria-label="Main navigation">
            <div class="container-xxl">

                <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/"
                    aria-label="<?= e(SITE_NAME) ?> home">
                    <svg class="brand-mark" viewBox="0 0 34 34" aria-hidden="true" focusable="false">
                        <circle cx="17" cy="17" r="16" fill="#1E5B45" />
                        <path d="M17 25.5V15.5" stroke="#F6F3EA" stroke-width="2.4" stroke-linecap="round" />
                        <path d="M17 16.6c.2-4.8-2.9-7.8-7.4-7.9.1 4.8 3 7.7 7.4 7.9Z" fill="#8FC7A6" />
                        <path d="M17 16.6c-.2-4.8 2.9-7.8 7.4-7.9-.1 4.8-3 7.7-7.4 7.9Z" fill="#C9E3CE" />
                    </svg>
                    <span class="brand-name">StudyGrove</span>
                </a>

                <!-- Bootstrap collapse: the menu turns into a burger button on
                 small screens. data-bs-* attributes are read by Bootstrap's
                 JavaScript bundle, loaded at the end of the page. -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Show or hide the navigation menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">

                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
                        <?php $onQuestions = $isSection(['', 'home', 'question']); ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $onQuestions ? ' active' : '' ?>" href="<?= BASE_URL ?>/"
                                <?= $onQuestions ? ' aria-current="page"' : '' ?>>Questions</a>
                        </li>

                        <?php if (Auth::isAdmin()): ?>
                            <!-- The admin receives the contact messages, so they see the
                             Inbox instead of the "contact the admin" form. -->
                            <li class="nav-item d-none d-lg-flex align-items-center" aria-hidden="true">
                                <span class="nav-divider"></span>
                            </li>

                            <?php $onModules = $isSection(['module']); ?>
                            <li class="nav-item">
                                <a class="nav-link nav-admin<?= $onModules ? ' active' : '' ?>"
                                    href="<?= BASE_URL ?>/module"
                                    <?= $onModules ? ' aria-current="page"' : '' ?>>Modules</a>
                            </li>

                            <?php $onUsers = $isSection(['user']); ?>
                            <li class="nav-item">
                                <a class="nav-link nav-admin<?= $onUsers ? ' active' : '' ?>" href="<?= BASE_URL ?>/user"
                                    <?= $onUsers ? ' aria-current="page"' : '' ?>>Users</a>
                            </li>

                            <?php $onInbox = $isSection(['contact']); ?>
                            <li class="nav-item">
                                <a class="nav-link nav-admin<?= $onInbox ? ' active' : '' ?>"
                                    href="<?= BASE_URL ?>/contact/inbox" <?= $onInbox ? ' aria-current="page"' : '' ?>>
                                    Inbox
                                    <?php if ($unreadMessages > 0): ?>
                                        <!-- A red badge counts the messages the admin
                                         has not opened yet. -->
                                        <span class="badge rounded-pill text-bg-danger ms-1"><?= (int) $unreadMessages ?>
                                            <span class="visually-hidden">unread message(s)</span>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <?php $onContact = $isSection(['contact']); ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $onContact ? ' active' : '' ?>" href="<?= BASE_URL ?>/contact"
                                    <?= $onContact ? ' aria-current="page"' : '' ?>>Contact</a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <?php if (Auth::check()): $me = Auth::user(); ?>
                            <?php /* A small round avatar with initials: AD for an
                                 administrator, ME for an ordinary member. */ ?>
                            <span class="avatar <?= Auth::isAdmin() ? 'avatar-admin' : 'avatar-member' ?>"
                                aria-hidden="true"><?= Auth::isAdmin() ? 'AD' : 'ME' ?></span>

                            <span class="d-flex flex-column lh-sm me-1">
                                <span class="fw-bold small"><?= e($me['full_name']) ?></span>
                                <span class="text-body-secondary" style="font-size:.75rem;">
                                    <?= Auth::isAdmin() ? 'admin' : 'user' ?>
                                </span>
                            </span>

                            <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/auth/logout">Log out</a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/auth/login">Log in</a>
                            <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/auth/register">Sign up</a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" class="container-xxl flex-grow-1 pt-4 pt-lg-5 pb-5">

        <?php $flashMessage = get_flash(); ?>
        <?php if ($flashMessage): ?>
            <?php
            // The flash type already matches a Bootstrap contextual class
            // ("danger", "warning"); anything else is shown as a success note.
            $alertClass = in_array($flashMessage['type'], ['danger', 'warning', 'info'], true)
                ? $flashMessage['type']
                : 'success';
            ?>
            <div class="alert alert-<?= e($alertClass) ?>"
                role="<?= $flashMessage['type'] === 'danger' ? 'alert' : 'status' ?>">
                <?= e($flashMessage['text']) ?>
            </div>
        <?php endif; ?>