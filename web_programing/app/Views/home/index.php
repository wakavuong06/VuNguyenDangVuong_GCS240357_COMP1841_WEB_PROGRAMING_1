<?php

use App\Core\Auth;
?>

<div class="row board-row">

    <!-- ---------- left sidebar: filter by module ---------- -->
    <aside class="col-lg-5 col-xl-4 mb-4 mb-lg-0">

        <div class="d-grid mb-4">
            <a class="btn btn-primary" href="<?= BASE_URL ?>/question/create">Ask a question</a>
        </div>

        <nav aria-label="Browse by module">
            <h2 class="side-title mb-2">Modules</h2>

            <div class="list-group">
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-2<?= $moduleId ? '' : ' active' ?>"
                    href="<?= BASE_URL ?>/" <?= $moduleId ? '' : ' aria-current="true"' ?>>
                    <span>All questions</span>
                    <span class="badge rounded-pill count"><?= (int) $total ?></span>
                </a>

                <?php foreach ($modules as $module): ?>
                <?php $onThisModule = $moduleId === (int) $module['id']; ?>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-2<?= $onThisModule ? ' active' : '' ?>"
                    href="<?= BASE_URL ?>/?module=<?= (int) $module['id'] ?>"
                    <?= $onThisModule ? ' aria-current="true"' : '' ?>>
                    <span class="text-break"><strong><?= e($module['code']) ?></strong> -
                        <?= e($module['name']) ?></span>
                    <span class="badge rounded-pill count"><?= (int) $module['question_count'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <?php if (!Auth::check()): ?>
        <div class="card mt-4 d-none d-lg-block">
            <div class="card-body">
                <h2 class="side-title mb-2">New here?</h2>
                <p class="small text-body-secondary">Create a free account to post questions and help other students.
                </p>
                <div class="d-grid">
                    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/auth/register">Sign up</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </aside>

    <!-- ---------- main column: search + question list ---------- -->
    <section class="col-lg-7 col-xl-8">

        <form class="input-group mb-3" method="get" action="<?= BASE_URL ?>/" role="search">
            <label class="visually-hidden" for="q">Search questions</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($keyword) ?>"
                placeholder="Search titles and details...">
            <?php if ($moduleId): ?>
            <input type="hidden" name="module" value="<?= (int) $moduleId ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <h1 class="h4 mb-3"><?= (int) $total ?> question<?= $total === 1 ? '' : 's' ?></h1>

        <?php if (!$questions): ?>

        <div class="text-center text-body-secondary py-5">
            <p class="mb-1">No questions match your search.</p>
            <p class="mb-0"><a href="<?= BASE_URL ?>/">Show every question</a></p>
        </div>

        <?php else: ?>

        <div class="card">
            <div class="list-group list-group-flush">
                <?php foreach ($questions as $row): ?>
                <article class="list-group-item py-3">
                    <h2 class="q-title h5 mb-1">
                        <a href="<?= BASE_URL ?>/question/show/<?= (int) $row['id'] ?>">
                            <?= e($row['title']) ?>
                        </a>
                    </h2>

                    <p class="q-excerpt text-body-secondary mb-2"><?= e(excerpt($row['body'])) ?></p>

                    <p class="d-flex flex-wrap align-items-center gap-2 small text-body-secondary mb-0">
                        <span><?= e($row['author_name']) ?></span>
                        <span class="module-chip"><?= e($row['module_code']) ?></span>
                        <span><?= e(nice_date($row['created_at'])) ?></span>
                        <?php if (!empty($row['image'])): ?>
                        <span>has a screenshot</span>
                        <?php endif; ?>
                    </p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
        <?php
                // Keep the current search and module filter in the links.
                $query = '';
                if ($keyword !== '') {
                    $query .= '&q=' . urlencode($keyword);
                }
                if ($moduleId) {
                    $query .= '&module=' . (int) $moduleId;
                }
                ?>
        <nav class="mt-4" aria-label="Pages">
            <ul class="pagination justify-content-center mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= BASE_URL ?>/?page=<?= $page - 1 ?><?= $query ?>">Previous</a>
                </li>
                <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                <?php endif; ?>

                <li class="page-item disabled">
                    <span class="page-link">Page <?= $page ?> of <?= $totalPages ?></span>
                </li>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= BASE_URL ?>/?page=<?= $page + 1 ?><?= $query ?>">Next</a>
                </li>
                <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Next</span></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php endif; ?>
    </section>
</div>