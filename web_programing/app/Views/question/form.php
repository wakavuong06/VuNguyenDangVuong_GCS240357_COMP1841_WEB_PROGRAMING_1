<?php

use App\Core\Auth;

$isEdit = $question !== null;
$action = $isEdit ? '/question/update/' . (int) $question->id : '/question/store';
?>

<div class="form-width">
    <h1><?= $isEdit ? 'Edit question' : 'Ask a question' ?></h1>
    <p class="text-body-secondary">
        <?= $isEdit
            ? 'Make your changes below, then save.'
            : 'Explain what you are stuck on - the more detail, the better the answers.' ?>
    </p>

    <?php if ($errors): ?>
    <div class="alert alert-danger" role="alert">Please fix the highlighted fields below.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL . $action ?>" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label" for="title">Title <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>" id="title"
                name="title" required minlength="5" maxlength="150"
                value="<?= e(isset($old['title']) ? $old['title'] : '') ?>">
            <?php if (isset($errors['title'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['title']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="module_id">Module <span class="req" aria-hidden="true">*</span></label>
            <select class="form-select<?= isset($errors['module_id']) ? ' is-invalid' : '' ?>" id="module_id"
                name="module_id" required>
                <option value="">-- Choose the module --</option>
                <?php foreach ($modules as $module): ?>
                <option value="<?= (int) $module['id'] ?>"
                    <?= (isset($old['module_id']) && (int) $old['module_id'] === (int) $module['id']) ? 'selected' : '' ?>>
                    <?= e($module['code']) ?> - <?= e($module['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['module_id'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['module_id']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (Auth::isAdmin()): ?>
        <!-- Members always post as themselves, so they never see this.
                 An admin can post for, or move a question to, another student. -->
        <div class="mb-3">
            <label class="form-label" for="user_id">Author <span class="req" aria-hidden="true">*</span></label>
            <select class="form-select<?= isset($errors['user_id']) ? ' is-invalid' : '' ?>" id="user_id" name="user_id"
                required>
                <?php foreach ($users as $u): ?>
                <option value="<?= (int) $u['id'] ?>"
                    <?= (int) (isset($old['user_id']) ? $old['user_id'] : Auth::id()) === (int) $u['id'] ? 'selected' : '' ?>>
                    <?= e($u['full_name']) ?> (<?= e($u['username']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['user_id'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['user_id']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label" for="body">Details <span class="req" aria-hidden="true">*</span></label>
            <textarea class="form-control<?= isset($errors['body']) ? ' is-invalid' : '' ?>" id="body" name="body"
                rows="9" required minlength="20"
                aria-describedby="body-help"><?= e(isset($old['body']) ? $old['body'] : '') ?></textarea>
            <div class="form-text" id="body-help">What did you try? What error message did you get?</div>
            <?php if (isset($errors['body'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['body']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="image">Screenshot <span class="optional">(optional)</span></label>

            <?php if ($isEdit && $question->image): ?>
            <img class="current-image img-thumbnail d-block mb-2"
                src="<?= PUBLIC_FOLDER ?>/uploads/<?= e($question->image) ?>"
                alt="The screenshot currently attached to this question">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                <label class="form-check-label" for="remove_image">Remove this image</label>
            </div>
            <?php endif; ?>

            <input type="file" class="form-control<?= isset($errors['image']) ? ' is-invalid' : '' ?>" id="image"
                name="image" accept="image/*" aria-describedby="image-help">
            <div class="form-text" id="image-help">JPG, PNG, GIF or WEBP, up to 2 MB.</div>
            <?php if (isset($errors['image'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['image']) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save changes' : 'Post question' ?>
            </button>
            <a class="btn btn-outline-secondary"
                href="<?= BASE_URL . ($isEdit ? '/question/show/' . (int) $question->id : '/') ?>">Cancel</a>
        </div>
    </form>
</div>