<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-dice"></i> Loten Trekken</h1>
            <p class="lead text-muted">Maak een groep en trek loten om cadeaus uit te wisselen</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('drawings/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nieuwe Loting
            </a>
        </div>
    </div>

    <!-- My Drawings -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-crown"></i> Mijn Lotingen</h3>
        
        <?php if (!empty($myDrawings)): ?>
            <div class="row">
                <?php foreach ($myDrawings as $drawing): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?= esc($drawing['title']) ?></h5>
                                    <span class="badge bg-<?= $drawing['status'] === 'drawn' ? 'success' : ($drawing['status'] === 'completed' ? 'info' : 'warning') ?>">
                                        <?= ucfirst($drawing['status']) ?>
                                    </span>
                                </div>
                                
                                <?php if ($drawing['description']): ?>
                                    <p class="card-text text-muted small"><?= esc(character_limiter($drawing['description'], 100)) ?></p>
                                <?php endif; ?>
                                
                                <?php if ($drawing['event_date']): ?>
                                    <p class="card-text small">
                                        <i class="fas fa-calendar"></i> <?= date('d-m-Y', strtotime($drawing['event_date'])) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('drawings/view/' . $drawing['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Bekijken
                                    </a>
                                    <a href="<?= base_url('drawings/edit/' . $drawing['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i> Bewerken
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> U heeft nog geen lotingen aangemaakt. 
                <a href="<?= base_url('drawings/create') ?>">Maak er nu een aan</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pending Invitations -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-envelope"></i> Uitnodigingen in Afwachting</h3>
        
        <?php 
        // Get current user ID from session
        $currentUserId = session()->get('user_id');
        
        // Filter for pending invitations where current user is the participant (not the creator)
        $pendingInvitations = array_filter($participatingDrawings ?? [], function($p) use ($currentUserId) {
            // Only show if status is pending
            if (($p['status'] ?? 'pending') !== 'pending') {
                return false;
            }
            
            // Only show if current user is the invited participant
            if ($p['user_id'] != $currentUserId) {
                return false;
            }
            
            // Only show if current user is NOT the creator (edge case)
            if (isset($p['creator_id']) && $p['creator_id'] == $currentUserId) {
                return false;
            }
            
            return true;
        });
        ?>
        
        <?php if (!empty($pendingInvitations)): ?>
            <div class="row">
                <?php foreach ($pendingInvitations as $invitation): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?= esc($invitation['drawing_title']) ?></h5>
                                    <span class="badge bg-warning">Uitnodiging</span>
                                </div>
                                
                                <p class="card-text text-muted small mb-3">
                                    U bent uitgenodigd om deel te nemen aan deze loting
                                </p>
                                
                                <?php if ($invitation['event_date']): ?>
                                    <p class="card-text small text-muted mb-3">
                                        <i class="fas fa-calendar"></i> <?= date('d-m-Y', strtotime($invitation['event_date'])) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('drawings/accept-invitation/' . $invitation['id']) ?>" class="btn btn-sm btn-success flex-grow-1">
                                        <i class="fas fa-check"></i> Accepteren
                                    </a>
                                    <a href="<?= base_url('drawings/decline-invitation/' . $invitation['id']) ?>" class="btn btn-sm btn-danger flex-grow-1">
                                        <i class="fas fa-times"></i> Weigeren
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> U heeft geen openstaande uitnodigingen.
            </div>
        <?php endif; ?>
    </div>

    <!-- Accepted Participations -->
    <div>
        <h3 class="mb-3"><i class="fas fa-users"></i> Deelnemingen</h3>
        
        <?php 
        // Get current user ID from session
        $currentUserId = session()->get('user_id');
        
        // Filter for accepted participations where current user is the participant (not the creator)
        $acceptedParticipations = array_filter($participatingDrawings ?? [], function($p) use ($currentUserId) {
            // Only show if status is accepted
            if (($p['status'] ?? 'pending') !== 'accepted') {
                return false;
            }
            
            // Only show if current user is the participant
            if ($p['user_id'] != $currentUserId) {
                return false;
            }
            
            // Only show if current user is NOT the creator (edge case)
            if (isset($p['creator_id']) && $p['creator_id'] == $currentUserId) {
                return false;
            }
            
            return true;
        });
        ?>
        
        <?php if (!empty($acceptedParticipations)): ?>
            <div class="row">
                <?php foreach ($acceptedParticipations as $participation): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?= esc($participation['drawing_title']) ?></h5>
                                    <span class="badge bg-<?= $participation['assigned_to_user_id'] ? 'success' : 'warning' ?>">
                                        <?= $participation['assigned_to_user_id'] ? 'Getrokken' : 'Wachtend' ?>
                                    </span>
                                </div>
                                
                                <?php if ($participation['assigned_to_user_id']): ?>
                                    <p class="card-text">
                                        <strong>U moet kopen voor:</strong> 
                                        <?= esc($participation['assigned_first_name'] . ' ' . $participation['assigned_last_name']) ?>
                                    </p>
                                    <?php if ($participation['list_id']): ?>
                                        <a href="<?= base_url('list/' . $participation['list_slug']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-gift"></i> Bekijk Verlanglijst
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="card-text text-muted">Wacht op de loting...</p>
                                <?php endif; ?>
                                
                                <?php if ($participation['event_date']): ?>
                                    <p class="card-text small text-muted mt-2">
                                        <i class="fas fa-calendar"></i> <?= date('d-m-Y', strtotime($participation['event_date'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> U neemt nog niet deel aan lotingen.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
