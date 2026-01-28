<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Gebruikers Beheren</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gebruikersnaam</th>
                            <th>E-mail</th>
                            <th>Naam</th>
                            <th>Rol</th>
                            <th>Status</th>
                            <th>Gemaakt op</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                        <?= esc($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'blocked' ? 'danger' : 'warning') ?>">
                                        <?= esc($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/user/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != session()->get('user_id')): ?>
                                            <a href="<?= base_url('admin/user/delete/' . $user['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
