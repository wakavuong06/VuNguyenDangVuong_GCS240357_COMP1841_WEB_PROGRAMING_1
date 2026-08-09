<?php
use App\Core\Auth;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h1 class="mb-0">Manage users</h1>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/user/create">Add a user</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <caption class="visually-hidden">All user accounts</caption>
            <thead>
                <tr>
                    <th scope="col">User</th>
                    <th scope="col">Username</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Role</th>
                    <th scope="col" class="text-end">Questions</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <span class="fw-bold"><?= e($user['full_name']) ?></span>
                        <?php if (Auth::id() === (int) $user['id']): ?>
                        <span class="text-body-secondary">(you)</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($user['username']) ?></td>
                    <td class="text-body-secondary"><?= e($user['email']) ?></td>
                    <td>
                        <span class="badge rounded-pill chip-<?= $user['role'] === 'admin' ? 'admin' : 'member' ?>">
                            <?= e(ucfirst($user['role'])) ?>
                        </span>
                    </td>
                    <td class="text-end"><?= (int) $user['question_count'] ?></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a class="btn btn-outline-secondary btn-sm"
                                href="<?= BASE_URL ?>/user/edit/<?= (int) $user['id'] ?>">Edit</a>
                            <?php if (Auth::id() !== (int) $user['id']): ?>
                            <form method="post" action="<?= BASE_URL ?>/user/delete/<?= (int) $user['id'] ?>"
                                data-confirm="Delete <?= e($user['username']) ?>? Their <?= (int) $user['question_count'] ?> question(s) will be deleted with the account.">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>