<?php /** @var array $modules */ ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h1 class="mb-0">Manage modules</h1>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/module/create">Add a module</a>
</div>

<?php if (empty($modules)): ?>
    <p class="text-body-secondary">No modules yet - add the first one so questions have somewhere to live.</p>
<?php else: ?>
    <!-- .table-responsive lets a wide table scroll sideways on a phone
         instead of breaking the page layout. -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <caption class="visually-hidden">All modules with their question counts</caption>
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col" class="text-end">Questions</th>
                        <th scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $module): ?>
                        <tr>
                            <td><span class="module-chip"><?= e($module['code']) ?></span></td>
                            <td><?= e($module['name']) ?></td>
                            <td class="text-body-secondary"><?= e($module['description'] ?? '') ?></td>
                            <td class="text-end"><?= (int) $module['question_count'] ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a class="btn btn-outline-secondary btn-sm"
                                       href="<?= BASE_URL ?>/module/edit/<?= (int) $module['id'] ?>">Edit</a>
                                    <form method="post" action="<?= BASE_URL ?>/module/delete/<?= (int) $module['id'] ?>"
                                          data-confirm="Delete the module <?= e($module['code']) ?>?">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
