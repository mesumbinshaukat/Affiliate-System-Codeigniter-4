<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-dice"></i> Drawing Events Management</h1>
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

    <?php if (!empty($drawings)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Drawing Title</th>
                        <th>Creator</th>
                        <th>Created Date</th>
                        <th>Event Date</th>
                        <th>Participants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drawings as $drawing): ?>
                        <tr>
                            <td>
                                <strong><?= esc($drawing['title']) ?></strong>
                            </td>
                            <td>
                                <?= esc($drawing['creator_first_name'] . ' ' . $drawing['creator_last_name']) ?>
                                <br>
                                <small class="text-muted">@<?= esc($drawing['creator_username']) ?></small>
                            </td>
                            <td>
                                <small><?= date('d-m-Y H:i', strtotime($drawing['created_at'])) ?></small>
                            </td>
                            <td>
                                <?php if ($drawing['event_date']): ?>
                                    <small><?= date('d-m-Y', strtotime($drawing['event_date'])) ?></small>
                                <?php else: ?>
                                    <small class="text-muted">Not set</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $drawing['total_participants'] ?? 0 ?> Total</span>
                                <br>
                                <small>
                                    <span class="badge bg-success"><?= $drawing['accepted_count'] ?? 0 ?> Accepted</span>
                                    <span class="badge bg-warning"><?= $drawing['pending_count'] ?? 0 ?> Pending</span>
                                    <span class="badge bg-danger"><?= $drawing['declined_count'] ?? 0 ?> Declined</span>
                                </small>
                            </td>
                            <td>
                                <?php if ($drawing['status'] === 'completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php elseif ($drawing['status'] === 'in_progress'): ?>
                                    <span class="badge bg-primary">In Progress</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/drawing/details/' . $drawing['id']) ?>" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                                <a href="<?= base_url('admin/drawing/delete/' . $drawing['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this drawing event and all its participants?');" title="Delete">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No drawing events found.
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
