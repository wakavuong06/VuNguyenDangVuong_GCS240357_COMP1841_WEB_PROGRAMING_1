<?php /** @var array $errors  @var array $old */ ?>

<div class="form-narrow">
    <h1>Contact the administrator</h1>
    <p class="text-body-secondary">Found a bug, or need help with your account? Send a message - it goes straight to the site administrator.</p>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields below.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/contact/send" novalidate="">

        <div class="mb-3">
            <label class="form-label" for="sender_name">Your name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['sender_name']) ? ' is-invalid' : '' ?>"
                   id="sender_name" name="sender_name" required minlength="2" maxlength="100"
                   autocomplete="name" value="<?= e($old['sender_name'] ?? '') ?>"
                   <?= isset($errors['sender_name']) ? 'aria-describedby="err-sender-name"' : '' ?>>
            <?php if (isset($errors['sender_name'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-sender-name"><?= e($errors['sender_name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="sender_email">Your e-mail <span class="req" aria-hidden="true">*</span></label>
            <input type="email" class="form-control<?= isset($errors['sender_email']) ? ' is-invalid' : '' ?>"
                   id="sender_email" name="sender_email" required maxlength="120"
                   autocomplete="email" value="<?= e($old['sender_email'] ?? '') ?>"
                   <?= isset($errors['sender_email']) ? 'aria-describedby="err-sender-email"' : '' ?>>
            <?php if (isset($errors['sender_email'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-sender-email"><?= e($errors['sender_email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="subject">Subject <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['subject']) ? ' is-invalid' : '' ?>"
                   id="subject" name="subject" required minlength="3" maxlength="150"
                   value="<?= e($old['subject'] ?? '') ?>"
                   <?= isset($errors['subject']) ? 'aria-describedby="err-subject"' : '' ?>>
            <?php if (isset($errors['subject'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-subject"><?= e($errors['subject']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="body">Message <span class="req" aria-hidden="true">*</span></label>
            <textarea class="form-control<?= isset($errors['body']) ? ' is-invalid' : '' ?>"
                      id="body" name="body" rows="7" required minlength="10"
                      <?= isset($errors['body']) ? 'aria-describedby="err-body"' : '' ?>><?= e($old['body'] ?? '') ?></textarea>
            <?php if (isset($errors['body'])): ?>
                <p class="invalid-feedback d-block mb-0" id="err-body"><?= e($errors['body']) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Send message</button>
        </div>
    </form>
</div>
