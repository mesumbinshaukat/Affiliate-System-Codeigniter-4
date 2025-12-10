<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Gebruiker Bewerken: <?= esc($editUser['username']) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('index.php/admin/user/edit/' . $editUser['id']) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Voornaam</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= esc($editUser['first_name']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Achternaam</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= esc($editUser['last_name']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Gebruikersnaam</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= esc($editUser['username']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc($editUser['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rol</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?= $editUser['role'] === 'user' ? 'selected' : '' ?>>Gebruiker</option>
                                <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Beheerder</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $editUser['status'] === 'active' ? 'selected' : '' ?>>Actief</option>
                                <option value="blocked" <?= $editUser['status'] === 'blocked' ? 'selected' : '' ?>>Geblokkeerd</option>
                                <option value="pending" <?= $editUser['status'] === 'pending' ? 'selected' : '' ?>>In afwachting</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nieuw Wachtwoord (laat leeg om huidig te behouden)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Gebruiker Bijwerken</button>
                            <a href="<?= base_url('index.php/admin/users') ?>" class="btn btn-secondary">Annuleren</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
