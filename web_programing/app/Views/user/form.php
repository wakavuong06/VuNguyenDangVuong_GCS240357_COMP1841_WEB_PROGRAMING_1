<?php

$isEdit = $user !== null;
$action = $isEdit ? '/user/update/' . (int) $user->id : '/user/store';
?>

<div class="form-narrow">
    <h1><?= $isEdit ? 'Edit user' : 'Add a user' ?></h1>
    <?php if ($isEdit): ?>
    <p class="text-body-secondary">Editing the account <strong><?= e($user->username) ?></strong>.</p>
    <?php endif; ?>

    <?php if ($errors): ?>
    <div class="alert alert-danger" role="alert">Please fix the highlighted fields below.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL . $action ?>">

        <div class="mb-3">
            <label class="form-label" for="full_name">Full name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>"
                id="full_name" name="full_name" required minlength="2" maxlength="100"
                value="<?= e(isset($old['full_name']) ? $old['full_name'] : '') ?>">
            <?php if (isset($errors['full_name'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['full_name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="username">Username <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>" id="username"
                name="username" required pattern="[a-zA-Z0-9._]{3,30}" aria-describedby="username-help"
                value="<?= e(isset($old['username']) ? $old['username'] : '') ?>">
            <div class="form-text" id="username-help">3-30 characters: letters, numbers, dots or underscores.</div>
            <?php if (isset($errors['username'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['username']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">E-mail <span class="req" aria-hidden="true">*</span></label>
            <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email"
                name="email" required maxlength="120" value="<?= e(isset($old['email']) ? $old['email'] : '') ?>">
            <?php if (isset($errors['email'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="role">Role</label>
            <select class="form-select" id="role" name="role">
                <option value="member"
                    <?= (isset($old['role']) ? $old['role'] : 'member') === 'member' ? 'selected' : '' ?>>Member
                </option>
                <option value="admin" <?= (isset($old['role']) ? $old['role'] : '') === 'admin' ? 'selected' : '' ?>>
                    Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">
                <?= $isEdit ? 'New password' : 'Password' ?>
                <?= $isEdit
                    ? '<span class="optional">(leave blank to keep the current one)</span>'
                    : '<span class="req" aria-hidden="true">*</span>' ?>
            </label>
            <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                id="password" name="password" minlength="8" aria-describedby="password-help"
                <?= $isEdit ? '' : 'required' ?> autocomplete="new-password">
            <div class="form-text" id="password-help">At least 8 characters.</div>
            <?php if (isset($errors['password'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirm">Repeat password</label>
            <input type="password" class="form-control<?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>"
                id="password_confirm" name="password_confirm" minlength="8" <?= $isEdit ? '' : 'required' ?>
                autocomplete="new-password">
            <?php if (isset($errors['password_confirm'])): ?>
            <p class="invalid-feedback d-block mb-0"><?= e($errors['password_confirm']) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Create account' ?></button>
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/user">Cancel</a>
        </div>
    </form>
</div>