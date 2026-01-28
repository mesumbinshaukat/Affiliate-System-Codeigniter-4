<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-dice"></i> Drawing Event Details</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('admin/drawings') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Drawings
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Drawing Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Drawing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Title</label>
                            <p class="h5"><?= esc($drawing['title']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <?php if ($drawing['status'] === 'completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php elseif ($drawing['status'] === 'in_progress'): ?>
                                    <span class="badge bg-primary">In Progress</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($drawing['description']): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Description</label>
                            <p><?= esc($drawing['description']) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created Date</label>
                            <p><?= date('d-m-Y H:i:s', strtotime($drawing['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Event Date</label>
                            <p>
                                <?php if ($drawing['event_date']): ?>
                                    <?= date('d-m-Y', strtotime($drawing['event_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Creator Information -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Creator Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Name</label>
                            <p class="h6"><?= esc($drawing['creator_first_name'] . ' ' . $drawing['creator_last_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Username</label>
                            <p class="h6">@<?= esc($drawing['creator_username']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants List -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Participants (<?= count($participants) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($participants)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Participant Name</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Wishlist</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($participant['first_name'] . ' ' . $participant['last_name']) ?></strong>
                                            </td>
                                            <td>
                                                <small>@<?= esc($participant['username']) ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = $participant['status'] ?? 'pending';
                                                if ($status === 'accepted'): 
                                                ?>
                                                    <span class="badge bg-success">Accepted</span>
                                                <?php elseif ($status === 'declined'): ?>
                                                    <span class="badge bg-danger">Declined</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($participant['assigned_to_user_id']): ?>
                                                    <small><?= esc($participant['assigned_first_name'] . ' ' . $participant['assigned_last_name']) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Not assigned</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($participant['list_title']): ?>
                                                    <small><?= esc($participant['list_title']) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">None</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No participants added yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Participant Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Participants</span>
                            <span class="badge bg-info"><?= $drawing['total_participants'] ?? 0 ?></span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" style="width: 100%">
                                <?= $drawing['total_participants'] ?? 0 ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Accepted</span>
                            <span class="badge bg-success"><?= $drawing['accepted_count'] ?? 0 ?></span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: <?= ($drawing['total_participants'] > 0) ? (($drawing['accepted_count'] ?? 0) / $drawing['total_participants'] * 100) : 0 ?>%">
                                <?= $drawing['accepted_count'] ?? 0 ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Pending</span>
                            <span class="badge bg-warning"><?= $drawing['pending_count'] ?? 0 ?></span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" style="width: <?= ($drawing['total_participants'] > 0) ? (($drawing['pending_count'] ?? 0) / $drawing['total_participants'] * 100) : 0 ?>%">
                                <?= $drawing['pending_count'] ?? 0 ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Declined</span>
                            <span class="badge bg-danger"><?= $drawing['declined_count'] ?? 0 ?></span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-danger" style="width: <?= ($drawing['total_participants'] > 0) ? (($drawing['declined_count'] ?? 0) / $drawing['total_participants'] * 100) : 0 ?>%">
                                <?= $drawing['declined_count'] ?? 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('admin/drawing/delete/' . $drawing['id']) ?>" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this drawing event and all its participants? This action cannot be undone.');">
                        <i class="fas fa-trash"></i> Delete Drawing Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
