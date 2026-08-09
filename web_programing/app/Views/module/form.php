<?php

/*
 * One form for BOTH adding and editing a module.
 * $module is null when adding, or the Module object when editing.
 */
$isEdit = $module !== null;
$action = $isEdit ? '/module/update/' . (int) $module->id : '/module/store';
?>

<div class="form-narrow">
    <h1><?= $isEdit ? 'Edit module' : 'Add a module' ?></h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields below.</div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL . $action ?>">

        <div class="mb-3">
            <label class="form-label" for="code">Module code <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['code']) ? ' is-invalid' : '' ?>"
                   id="code" name="code" required pattern="[A-Za-z0-9]{4,10}"
                   aria-describedby="code-help"
                   value="<?= e(isset($old['code']) ? $old['code'] : '') ?>">
            <div class="form-text" id="code-help">4-10 letters or numbers, e.g. COMP1841. Saved in capitals.</div>
            <?php if (isset($errors['code'])): ?>
                <p class="invalid-feedback d-block mb-0"><?= e($errors['code']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="name">Module name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                   id="name" name="name" required minlength="3" maxlength="100"
                   value="<?= e(isset($old['name']) ? $old['name'] : '') ?>">
            <?php if (isset($errors['name'])): ?>
                <p class="invalid-feedback d-block mb-0"><?= e($errors['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="description">Description <span class="optional">(optional)</span></label>
            <input type="text" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                   id="description" name="description" maxlength="255"
                   value="<?= e(isset($old['description']) ? $old['description'] : '') ?>">
            <?php if (isset($errors['description'])): ?>
                <p class="invalid-feedback d-block mb-0"><?= e($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Add module' ?></button>
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/module">Cancel</a>
        </div>
    </form>
</div>
