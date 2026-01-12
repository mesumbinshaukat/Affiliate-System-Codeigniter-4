<!-- Group Gift Contribution UI -->
<?php if (!empty($product['is_group_gift']) && !empty($product['target_amount'])): ?>
    <?php
    // Calculate contribution stats
    $contributionModel = new \App\Models\ContributionModel();
    $stats = $contributionModel->getProductStats($product['list_product_id']);
    $totalContributed = $stats['total_amount'];
    $targetAmount = (float) $product['target_amount'];
    $percentage = ($targetAmount > 0) ? min(($totalContributed / $targetAmount) * 100, 100) : 0;
    $remaining = max($targetAmount - $totalContributed, 0);
    $isComplete = $totalContributed >= $targetAmount;
    $contributorCount = $stats['contributor_count'];
    ?>

    <div class="card border-warning mb-3" id="groupGift_<?= $product['list_product_id'] ?>">
        <div class="card-body">
            <h6 class="card-title text-warning">
                <i class="fas fa-users"></i> Groepscadeau
                <?php if ($isComplete): ?>
                    <span class="badge bg-success float-end">Doel Bereikt! 🎉</span>
                <?php endif; ?>
            </h6>
            
            <!-- Progress Bar -->
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Verzameld: <strong>€<?= number_format($totalContributed, 2) ?></strong></small>
                    <small class="text-muted">Doel: <strong>€<?= number_format($targetAmount, 2) ?></strong></small>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $percentage ?>%;" 
                         aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= round($percentage) ?>%
                    </div>
                </div>
                <?php if (!$isComplete): ?>
                    <small class="text-muted">Nog €<?= number_format($remaining, 2) ?> te gaan</small>
                <?php endif; ?>
            </div>

            <!-- Contributor Count -->
            <?php if ($contributorCount > 0): ?>
                <p class="mb-3">
                    <i class="fas fa-heart text-danger"></i>
                    <strong><?= $contributorCount ?></strong> <?= $contributorCount == 1 ? 'persoon heeft' : 'mensen hebben' ?> bijgedragen
                </p>
            <?php endif; ?>

            <?php if (!$isComplete): ?>
                <!-- Contribution Form -->
                <button class="btn btn-warning btn-sm w-100" onclick="showContributionModal(<?= $product['list_product_id'] ?>, '<?= esc($product['title'], 'js') ?>', <?= $targetAmount ?>, <?= $remaining ?>)">
                    <i class="fas fa-hand-holding-heart"></i> Bijdragen
                </button>
            <?php else: ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i> Het doel is bereikt! Bedankt aan alle bijdragers.
                </div>
            <?php endif; ?>

            <!-- Show Contributors -->
            <?php if (!empty($stats['contributions'])): ?>
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-secondary w-100" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#contributors_<?= $product['list_product_id'] ?>" aria-expanded="false">
                        <i class="fas fa-list"></i> Toon Bijdragers (<?= count($stats['contributions']) ?>)
                    </button>
                    <div class="collapse mt-2" id="contributors_<?= $product['list_product_id'] ?>">
                        <div class="list-group">
                            <?php foreach (array_slice($stats['contributions'], 0, 10) as $contribution): ?>
                                <div class="list-group-item py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user-circle text-muted"></i>
                                            <strong><?= $contribution['is_anonymous'] ? 'Anoniem' : esc($contribution['contributor_name']) ?></strong>
                                            <?php if (!empty($contribution['message'])): ?>
                                                <br><small class="text-muted fst-italic">"<?= esc($contribution['message']) ?>"</small>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-success">€<?= number_format($contribution['amount'], 2) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
