<?php /** @var array $errors  @var array $old */ ?>

<div class="form-narrow">
    <h1>Create an account</h1>
    <p class="text-body-secondary">One quick form and you can start asking questions.</p>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields below.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/auth/registerPost" novalidate="">

        <div class="mb-3">
            <label class="form-label" for="full_name">Full name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>"
                   id="full_name" name="full_name" required minlength="2" maxlength="100"
                   autocomplete="name" value="<?= e($old['full_name'] ?? '') ?>"
                   <?= isset($errors['full_name']) ? 'aria-describedby="err-full-name"' : '' ?>>
            <?php if (isset($errors['full_name'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-full-name"><?= e($errors['full_name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="username">Username <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>"
                   id="username" name="username" required pattern="[a-zA-Z0-9._]{3,30}"
                   title="3-30 characters: letters, numbers, dots or underscores"
                   autocomplete="username" value="<?= e($old['username'] ?? '') ?>"
                   aria-describedby="<?= isset($errors['username']) ? 'err-username' : 'username-help' ?>">
            <div class="form-text" id="username-help">3–30 characters: letters, numbers, dots or underscores.</div>
            <?php if (isset($errors['username'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-username"><?= e($errors['username']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">E-mail <span class="req" aria-hidden="true">*</span></label>
            <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                   id="email" name="email" required maxlength="120"
                   autocomplete="email" value="<?= e($old['email'] ?? '') ?>"
                   <?= isset($errors['email']) ? 'aria-describedby="err-email"' : '' ?>>
            <?php if (isset($errors['email'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-email"><?= e($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Password <span class="req" aria-hidden="true">*</span></label>
            <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                   id="password" name="password" required minlength="8"
                   autocomplete="new-password" aria-describedby="password-help">
            <div class="form-text" id="password-help">At least 8 characters.</div>
            <?php if (isset($errors['password'])): ?>
                <p class="invalid-feedback d-block mb-0"><?= e($errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirm">Repeat password <span class="req" aria-hidden="true">*</span></label>
            <input type="password" class="form-control<?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>"
                   id="password_confirm" name="password_confirm" required minlength="8"
                   autocomplete="new-password">
            <?php if (isset($errors['password_confirm'])): ?>
                <p class="invalid-feedback d-block mb-0"><?= e($errors['password_confirm']) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Create account</button>
        </div>

        <p class="small text-body-secondary mt-3 mb-0">
            Already have an account? <a href="<?= BASE_URL ?>/auth/login">Log in</a>
        </p>
    </form>
</div>
