<?php

/** @var array $errors  @var array $old */ ?>

<div class="form-narrow">
    <h1>Log in</h1>
    <p class="text-body-secondary">Welcome back to the grove.</p>

    <form method="post" action="<?= BASE_URL ?>/auth/loginPost" novalidate="">

        <div class="mb-3">
            <label class="form-label" for="identifier">E-mail or username</label>
            <input type="text" class="form-control<?= isset($errors['identifier']) ? ' is-invalid' : '' ?>"
                id="identifier" name="identifier" required autocomplete="username"
                value="<?= e($old['identifier'] ?? '') ?>"
                <?= isset($errors['identifier']) ? 'aria-describedby="err-identifier"' : '' ?>>
            <?php if (isset($errors['identifier'])): ?>
            <p class="invalid-feedback d-block mb-0" id="err-identifier"><?= e($errors['identifier']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required
                autocomplete="current-password">
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Log in</button>
        </div>

        <p class="small text-body-secondary mt-3 mb-0">
            <a href="<?= BASE_URL ?>/auth/register">Create an account</a>
        </p>
    </form>
</div>