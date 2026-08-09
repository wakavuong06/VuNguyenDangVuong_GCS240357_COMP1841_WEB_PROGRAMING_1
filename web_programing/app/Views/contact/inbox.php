<?php
/**
 * @var string $sort
 */
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h1 class="mb-0">Contact inbox</h1>

    <?php if (!empty($messages)): ?>
    <!-- Sort order, shown as a Bootstrap button group. Each button is
             just a link back to this page with a different ?sort= value. -->
    <div class="btn-group" role="group" aria-label="Sort messages">
        <a class="btn btn-sm <?= $sort === 'newest' ? 'btn-primary' : 'btn-outline-primary' ?>"
            href="<?= BASE_URL ?>/contact/inbox?sort=newest"
            <?= $sort === 'newest' ? 'aria-current="true"' : '' ?>>Newest first</a>
        <a class="btn btn-sm <?= $sort === 'oldest' ? 'btn-primary' : 'btn-outline-primary' ?>"
            href="<?= BASE_URL ?>/contact/inbox?sort=oldest"
            <?= $sort === 'oldest' ? 'aria-current="true"' : '' ?>>Oldest first</a>
    </div>
    <?php endif; ?>
</div>

<?php if (empty($messages)): ?>
<p class="text-body-secondary">No messages yet.</p>
<?php else: ?>
<div class="d-grid gap-3">
    <?php foreach ($messages as $message): ?>
    <article class="card">
        <div class="card-body">
            <h2 class="h5 card-title mb-1"><?= e($message['subject']) ?></h2>

            <p class="small text-body-secondary mb-3">
                From <strong><?= e($message['sender_name']) ?></strong>
                &lt;<a href="mailto:<?= e($message['sender_email']) ?>"><?= e($message['sender_email']) ?></a>&gt;
                · <time datetime="<?= e($message['created_at']) ?>"><?= e(nice_date($message['created_at'])) ?></time>
            </p>

            <p class="card-text mb-0"><?= nl2br(e($message['body'])) ?></p>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>