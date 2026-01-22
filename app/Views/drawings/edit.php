<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4"><i class="fas fa-edit"></i> Loting Bewerken: <?= esc($drawing['title']) ?></h1>

            <ul class="nav nav-tabs mb-4" id="drawingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                        <i class="fas fa-info-circle"></i> Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="participants-tab" data-bs-toggle="tab" data-bs-target="#participants" type="button">
                        <i class="fas fa-users"></i> Deelnemers (<?= count($participants) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="drawingTabsContent">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('index.php/drawings/edit/' . $drawing['id']) ?>">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titel *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= esc($drawing['title']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Beschrijving</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= esc($drawing['description']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="event_date" class="form-label">Datum van het evenement</label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" 
                                           value="<?= $drawing['event_date'] ? date('Y-m-d', strtotime($drawing['event_date'])) : '' ?>">
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Opslaan
                                    </button>
                                    <a href="<?= base_url('index.php/drawings') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Terug
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Participants Tab -->
                <div class="tab-pane fade" id="participants" role="tabpanel">
                    <!-- Add Participants Section -->
                    <div class="card mb-4" style="border-top: 4px dashed #E31E24;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0" style="color: #E31E24;">Deelnemers toevoegen</h5>
                                <i class="fas fa-trash" style="color: #ccc; cursor: pointer;"></i>
                            </div>
                            
                            <?php if ($drawing['status'] === 'pending'): ?>
                                <!-- Add by Email Form -->
                                <form method="post" action="<?= base_url('index.php/drawings/add-participant/' . $drawing['id']) ?>" class="mb-4">
                                    <div class="mb-3">
                                        <label class="form-label">Naam van de deelnemer</label>
                                        <input type="text" class="form-control" name="invite_name" placeholder="Bijv. Jan Jansen">
                                        <small class="text-muted">Optioneel, maar helpt om te verifiëren wie je toevoegt.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">E-mailadres *</label>
                                        <input type="email" class="form-control" name="invite_email" placeholder="naam@voorbeeld.nl" required>
                                        <small class="text-muted">Dit moet overeenkomen met het e-mailadres waarmee de persoon inlogt. Ze ontvangen hierop de uitnodiging.</small>
                                    </div>
                                    <button class="btn btn-danger" type="submit" style="background-color: #E31E24; border-color: #E31E24;">
                                        <i class="fas fa-plus"></i> Uitnodiging versturen
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning mb-4">
                                    <i class="fas fa-exclamation-triangle"></i> Deelnemers kunnen niet meer worden toegevoegd na de loting.
                                </div>
                            <?php endif; ?>

                            <!-- Current Participants List -->
                            <h6 class="mb-3"><strong><?= count($participants) ?> <?= count($participants) === 1 ? 'deelnemer' : 'deelnemers' ?></strong></h6>
                            
                            <?php if (!empty($participants)): ?>
                                <div class="mb-4">
                                    <?php 
                                    $creatorUser = null;
                                    foreach ($participants as $participant):
                                        // Check if this is the creator
                                        if ($participant['user_id'] == $drawing['creator_id']):
                                            $creatorUser = $participant;
                                        endif;
                                    endforeach;
                                    
                                    // Show creator first with "(this is you)" label
                                    if ($creatorUser):
                                    ?>
                                        <div class="p-3 bg-light rounded mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?= esc($creatorUser['first_name'] . ' ' . $creatorUser['last_name']) ?> <span style="color: #999;">(dit ben jij)</span></h6>
                                                    <small class="text-muted"><?= esc($creatorUser['email'] ?? $creatorUser['username']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Other participants -->
                                    <?php foreach ($participants as $participant): ?>
                                        <?php if ($participant['user_id'] != $drawing['creator_id']): ?>
                                            <div class="p-3 bg-light rounded mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?= esc($participant['first_name'] . ' ' . $participant['last_name']) ?></h6>
                                                        <small class="text-muted"><?= esc($participant['email'] ?? $participant['username']) ?></small>
                                                        <br>
                                                        <small>
                                                            <?php 
                                                            $participantStatus = $participant['status'] ?? 'pending';
                                                            if ($participantStatus === 'pending'): 
                                                            ?>
                                                                <span class="badge bg-warning">Uitnodiging verzonden</span>
                                                            <?php elseif ($participantStatus === 'accepted'): ?>
                                                                <span class="badge bg-success">Geaccepteerd</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Geweigerd</span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <?php if ($drawing['status'] === 'pending'): ?>
                                                        <a href="<?= base_url('index.php/drawings/remove-participant/' . $drawing['id'] . '/' . $participant['id']) ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Zeker weten?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>

                                <?php 
                                $acceptedCount = count(array_filter($participants, function($p) { return ($p['status'] ?? 'pending') === 'accepted'; }));
                                $totalCount = count($participants);
                                ?>

                                <?php if ($drawing['status'] === 'pending'): ?>
                                    <?php if ($acceptedCount >= 2): ?>
                                        <div class="alert alert-success mt-4">
                                            <i class="fas fa-check-circle"></i> <?= $acceptedCount ?> van <?= $totalCount ?> deelnemers hebben geaccepteerd. U kunt nu loten trekken.
                                        </div>
                                        <div class="mt-3">
                                            <a href="<?= base_url('index.php/drawings/draw/' . $drawing['id']) ?>" 
                                               class="btn btn-success btn-lg w-100"
                                               onclick="return confirm('Weet u zeker? Dit kan niet ongedaan gemaakt worden.')">
                                                <i class="fas fa-dice"></i> Trek Loten
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mt-4">
                                            <i class="fas fa-info-circle"></i> Wacht op acceptatie van deelnemers. Momenteel <?= $acceptedCount ?> van <?= $totalCount ?> geaccepteerd.
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Nog geen deelnemers toegevoegd.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Invitation Link Section -->
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <em>Nodig mensen uit, bijvoorbeeld via WhatsApp. Zo kunnen zij zichzelf toevoegen aan deze loting en ziet u het direct in de lijst hierboven.</em>
                            </p>
                            
                            <!-- Display the invitation link -->
                            <div class="mb-3 p-3 bg-light rounded">
                                <small class="text-muted d-block mb-2">Uitnodigingslink:</small>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" class="form-control form-control-sm" id="invitationLink" value="<?= base_url('index.php/drawings/view/' . $drawing['id']) ?>" readonly>
                                    <button class="btn btn-sm btn-danger" style="background-color: #E31E24; border-color: #E31E24; white-space: nowrap;" onclick="copyInvitationLink(this)">
                                        <i class="fas fa-copy"></i> Kopieer
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Share buttons -->
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/?text=<?= urlencode('Join my drawing "' . esc($drawing['title']) . '": ' . base_url('index.php/drawings/view/' . $drawing['id'])) ?>" target="_blank" class="btn btn-success flex-grow-1">
                                    <i class="fab fa-whatsapp"></i> Deel via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyInvitationLink(button) {
    const link = document.getElementById('invitationLink').value;
    
    // Copy to clipboard
    navigator.clipboard.writeText(link).then(() => {
        // Show success message
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Gekopieerd!';
        button.style.backgroundColor = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.backgroundColor = '#E31E24';
        }, 2000);
    }).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = link;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Gekopieerd!';
        button.style.backgroundColor = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.backgroundColor = '#E31E24';
        }, 2000);
    });
}
</script>
<?= $this->endSection() ?>
