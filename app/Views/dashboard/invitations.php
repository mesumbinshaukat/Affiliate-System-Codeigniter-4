<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope"></i> Lijstuitnodigingen</h2>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Terug
        </a>
    </div>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($invitations)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Geen uitnodigingen</h4>
                            <p class="text-muted">Je hebt op dit moment geen openstaande uitnodigingen om lijsten te beheren.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($invitations as $invitation): ?>
                            <div class="col-12 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h5 class="card-title mb-2">
                                                    <i class="fas fa-list text-primary"></i>
                                                    <?= esc($invitation['list_title']) ?>
                                                </h5>
                                                <p class="mb-2">
                                                    <strong><?= esc($invitation['inviter_first_name'] . ' ' . $invitation['inviter_last_name']) ?></strong>
                                                    (@<?= esc($invitation['inviter_username']) ?>) nodigt je uit om samen deze lijst te beheren
                                                </p>
                                                <?php if (!empty($invitation['message'])): ?>
                                                    <div class="alert alert-info alert-sm mb-2">
                                                        <i class="fas fa-comment"></i>
                                                        <em>"<?= esc($invitation['message']) ?>"</em>
                                                    </div>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    Uitgenodigd op <?= date('d-m-Y', strtotime($invitation['created_at'])) ?>
                                                    <?php if ($invitation['expires_at']): ?>
                                                        | Verloopt op <?= date('d-m-Y', strtotime($invitation['expires_at'])) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <a href="<?= base_url('collaboration/accept/' . $invitation['token']) ?>" 
                                                   class="btn btn-success mb-2">
                                                    <i class="fas fa-check"></i> Accepteren
                                                </a>
                                                <a href="<?= base_url('collaboration/reject/' . $invitation['token']) ?>" 
                                                   class="btn btn-outline-secondary mb-2"
                                                   onclick="return confirm('Weet je zeker dat je deze uitnodiging wilt afwijzen?')">
                                                    <i class="fas fa-times"></i> Afwijzen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

</div>

<?= $this->endSection() ?>
