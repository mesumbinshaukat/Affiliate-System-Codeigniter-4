<!-- Collaborator Management Component -->
<div class="card shadow-sm mb-4" id="collaborators-section">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-users"></i> Samenwerken
            <?php if (isset($is_owner) && $is_owner): ?>
                <span class="badge bg-primary">Eigenaar</span>
            <?php else: ?>
                <span class="badge bg-success">Medewerker</span>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Nodig anderen uit om samen aan deze lijst te werken. Medewerkers kunnen producten toevoegen, bewerken en verwijderen.
        </p>

        <!-- Invite Form (only for owners and collaborators with invite permission) -->
        <?php if (isset($is_owner) && $is_owner): ?>
            <div class="alert alert-info alert-sm mb-3">
                <i class="fas fa-info-circle"></i> Als eigenaar kun je medewerkers uitnodigen en beheren.
            </div>
            
            <form id="invite-form" class="mb-4">
                <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="E-mailadres van de persoon"
                               required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" 
                               name="message" 
                               class="form-control" 
                               placeholder="Persoonlijk bericht (optioneel)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Uitnodigen
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <!-- Collaborators List -->
        <div id="collaborators-list">
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Laden...</span>
                </div>
            </div>
        </div>

        <!-- Pending Invitations -->
        <div id="pending-invitations" style="display: none;">
            <hr class="my-3">
            <h6 class="text-muted mb-3">
                <i class="fas fa-clock"></i> Openstaande uitnodigingen
            </h6>
            <div id="invitations-list"></div>
        </div>

        <!-- Leave Collaboration (if not owner) -->
        <?php if (isset($is_owner) && !$is_owner): ?>
            <hr class="my-3">
            <form method="post" action="<?= base_url('collaboration/leave') ?>" 
                  onsubmit="return confirm('Weet je zeker dat je deze samenwerking wilt verlaten? Je verliest de toegang tot deze lijst.')">
                <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Samenwerking verlaten
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const listId = <?= $list['id'] ?>;
    const isOwner = <?= isset($is_owner) && $is_owner ? 'true' : 'false' ?>;

    // Load collaborators and invitations
    loadCollaborators();

    // Handle invite form submission
    const inviteForm = document.getElementById('invite-form');
    if (inviteForm) {
        inviteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(inviteForm);
            const submitBtn = inviteForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verzenden...';

            fetch('<?= base_url('collaboration/invite') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    inviteForm.reset();
                    loadCollaborators();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Er is een fout opgetreden bij het verzenden van de uitnodiging');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    function loadCollaborators() {
        fetch(`<?= base_url('collaboration/list/') ?>${listId}/collaborators`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCollaborators(data.collaborators, data.is_owner);
                    displayInvitations(data.invitations, data.is_owner);
                } else {
                    document.getElementById('collaborators-list').innerHTML = 
                        '<div class="alert alert-danger">Fout bij het laden van medewerkers</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('collaborators-list').innerHTML = 
                    '<div class="alert alert-danger">Fout bij het laden van medewerkers</div>';
            });
    }

    function displayCollaborators(collaborators, isOwner) {
        const container = document.getElementById('collaborators-list');
        
        if (collaborators.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0"><em>Nog geen medewerkers</em></p>';
            return;
        }

        let html = '<div class="list-group">';
        
        collaborators.forEach(collab => {
            const isCurrentOwner = collab.role === 'owner';
            const avatarUrl = collab.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(collab.first_name + '+' + collab.last_name) + '&background=E31E24&color=fff&size=40';
            
            html += `
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="${avatarUrl}" 
                                 alt="${collab.first_name}" 
                                 class="rounded-circle" 
                                 width="40" height="40"
                                 onerror="this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('${collab.first_name}+${collab.last_name}') + '&background=E31E24&color=fff&size=40'">
                        </div>
                        <div class="col">
                            <strong>${collab.first_name} ${collab.last_name}</strong>
                            <small class="text-muted d-block">@${collab.username}</small>
                        </div>
                        <div class="col-auto">
                            ${isCurrentOwner ? 
                                '<span class="badge bg-primary">Eigenaar</span>' : 
                                '<span class="badge bg-success">Medewerker</span>'}
                        </div>
                        ${isOwner && !isCurrentOwner ? `
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="removeCollaborator(${collab.user_id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    function displayInvitations(invitations, isOwner) {
        if (!isOwner || invitations.length === 0) {
            document.getElementById('pending-invitations').style.display = 'none';
            return;
        }

        document.getElementById('pending-invitations').style.display = 'block';
        const container = document.getElementById('invitations-list');
        
        let html = '<div class="list-group list-group-flush">';
        
        invitations.forEach(inv => {
            html += `
                <div class="list-group-item" id="invitation-${inv.id}">
                    <div class="row align-items-center">
                        <div class="col">
                            <i class="fas fa-envelope text-muted"></i>
                            <strong>${inv.invitee_email}</strong>
                            <small class="text-muted d-block">
                                Uitgenodigd op ${new Date(inv.created_at).toLocaleDateString('nl-NL')}
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-warning">In afwachting</span>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-secondary" 
                                    onclick="cancelInvitation(${inv.id})">
                                <i class="fas fa-times"></i> Annuleren
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    // Make functions global
    window.removeCollaborator = function(userId) {
        if (!confirm('Weet je zeker dat je deze medewerker wilt verwijderen?')) {
            return;
        }

        const formData = new FormData();
        formData.append('list_id', listId);
        formData.append('user_id', userId);

        fetch('<?= base_url('collaboration/remove') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                loadCollaborators();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Er is een fout opgetreden');
            console.error('Error:', error);
        });
    };

    window.cancelInvitation = function(invitationId) {
        if (!confirm('Weet je zeker dat je deze uitnodiging wilt annuleren?')) {
            return;
        }

        fetch(`<?= base_url('collaboration/cancel/') ?>${invitationId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                loadCollaborators();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Er is een fout opgetreden');
            console.error('Error:', error);
        });
    };

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.getElementById('collaborators-section');
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
});
</script>
