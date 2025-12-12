<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1><?= esc($drawing['title']) ?></h1>
            <p class="lead text-muted"><?= esc($drawing['description']) ?></p>
            
            <div class="d-flex gap-3 align-items-center">
                <span class="badge bg-<?= $drawing['status'] === 'drawn' ? 'success' : 'warning' ?>">
                    <?= $drawing['status'] === 'drawn' ? 'Getrokken' : 'Wachtend' ?>
                </span>
                
                <?php if ($drawing['event_date']): ?>
                    <span class="text-muted">
                        <i class="fas fa-calendar"></i> <?= date('d-m-Y', strtotime($drawing['event_date'])) ?>
                    </span>
                <?php endif; ?>
                
                <span class="text-muted">
                    <i class="fas fa-user"></i> Gemaakt door: <?= esc($drawing['creator_first_name'] . ' ' . $drawing['creator_last_name']) ?>
                </span>
            </div>
        </div>
        <?php if ($isCreator): ?>
            <div class="col-auto">
                <a href="<?= base_url('index.php/drawings/edit/' . $drawing['id']) ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i> Bewerken
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-users"></i> Deelnemers</h5>
                    
                    <?php if (!empty($participants)): ?>
                        <div class="row">
                            <?php foreach ($participants as $participant): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-light h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <?= esc($participant['first_name'] . ' ' . $participant['last_name']) ?>
                                            </h6>
                                            <p class="text-muted small mb-2">@<?= esc($participant['username']) ?></p>
                                            
                                            <?php if ($drawing['status'] === 'drawn' && $participant['assigned_to_user_id']): ?>
                                                <div class="alert alert-info mb-3">
                                                    <strong>Moet kopen voor:</strong><br>
                                                    <?= esc($participant['assigned_first_name'] . ' ' . $participant['assigned_last_name']) ?>
                                                </div>
                                                
                                                <?php if ($participant['list_id']): ?>
                                                    <a href="<?= base_url('index.php/list/' . $participant['list_slug']) ?>" 
                                                       class="btn btn-sm btn-primary w-100">
                                                        <i class="fas fa-gift"></i> Bekijk Verlanglijst
                                                    </a>
                                                <?php else: ?>
                                                    <div class="alert alert-warning alert-sm">
                                                        <small>Geen verlanglijst beschikbaar</small>
                                                    </div>
                                                <?php endif; ?>
                                            <?php elseif ($drawing['status'] === 'pending'): ?>
                                                <p class="text-muted small mb-0">Wacht op de loting...</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Geen deelnemers in deze loting.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
