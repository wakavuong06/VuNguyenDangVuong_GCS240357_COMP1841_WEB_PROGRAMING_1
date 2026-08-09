<?php

?>

<article class="reading-width">

    <p class="small mb-2"><a class="text-decoration-none" href="<?= BASE_URL ?>/">&larr; All questions</a></p>

    <header>
        <?php if ($module): ?>
        <p class="mb-2">
            <a class="module-chip" href="<?= BASE_URL ?>/?module=<?= (int) $module->id ?>">
                <?= e($module->code) ?> - <?= e($module->name) ?>
            </a>
        </p>
        <?php endif; ?>

        <h1 class="mb-2"><?= e($question->title) ?></h1>

        <p class="d-flex flex-wrap align-items-center gap-2 small text-body-secondary mb-0">
            <span>asked by <strong><?= e($author ? $author->full_name : 'Unknown') ?></strong></span>
            <time datetime="<?= e($question->created_at) ?>"><?= e(nice_date($question->created_at)) ?></time>
        </p>
    </header>

    <!-- e() escapes the text, nl2br() keeps the line breaks the user typed -->
    <div class="card my-4">
        <div class="card-body q-body">
            <?= nl2br(e($question->body)) ?>
        </div>
    </div>

    <?php if ($question->image): ?>
    <figure class="mb-4">
        <img class="img-fluid rounded border" src="<?= PUBLIC_FOLDER ?>/uploads/<?= e($question->image) ?>"
            alt="Screenshot attached to the question: <?= e($question->title) ?>">
        <figcaption class="small text-body-secondary mt-2">Attached screenshot</figcaption>
    </figure>
    <?php endif; ?>

    <?php if ($canManage): ?>
    <div class="d-flex flex-wrap gap-2 pt-3 border-top">
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/question/edit/<?= (int) $question->id ?>">Edit</a>

        <form method="post" action="<?= BASE_URL ?>/question/delete/<?= (int) $question->id ?>"
            data-confirm="Delete this question permanently? This cannot be undone.">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
    <?php endif; ?>

</article>