<!-- Contribution Modal -->
<div class="modal fade" id="contributionModal" tabindex="-1" aria-labelledby="contributionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contributionModalLabel">
                    <i class="fas fa-hand-holding-heart text-warning"></i> Bijdragen aan Groepscadeau
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contributionProductInfo" class="alert alert-info mb-3">
                    <!-- Product info will be inserted here -->
                </div>

                <form id="contributionForm">
                    <input type="hidden" id="contribution_list_product_id" name="list_product_id">
                    
                    <div class="mb-3">
                        <label for="contributor_name" class="form-label">Jouw Naam <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contributor_name" name="contributor_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="contributor_email" class="form-label">E-mail (optioneel)</label>
                        <input type="email" class="form-control" id="contributor_email" name="contributor_email">
                        <small class="text-muted">Voor een bevestigingsbericht</small>
                    </div>

                    <div class="mb-3">
                        <label for="contribution_amount" class="form-label">Bedrag (€) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="contribution_amount" name="amount" 
                                   step="0.01" min="0.01" required>
                        </div>
                        <small class="text-muted" id="remainingAmount"><!-- Will be updated dynamically --></small>
                    </div>

                    <div class="mb-3">
                        <label for="contribution_message" class="form-label">Bericht (optioneel)</label>
                        <textarea class="form-control" id="contribution_message" name="message" rows="3" 
                                  placeholder="Laat een leuke boodschap achter..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                            <label class="form-check-label" for="is_anonymous">
                                Anoniem bijdragen (je naam wordt niet getoond)
                            </label>
                        </div>
                    </div>

                    <div id="contributionError" class="alert alert-danger d-none"></div>
                    <div id="contributionSuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-warning" onclick="submitContribution()">
                    <i class="fas fa-heart"></i> Bijdragen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let contributionModal;

document.addEventListener('DOMContentLoaded', function() {
    contributionModal = new bootstrap.Modal(document.getElementById('contributionModal'));
});

function formatCurrency(amount) {
    return amount.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showContributionModal(listProductId, productTitle, targetAmount, remaining) {
    document.getElementById('contribution_list_product_id').value = listProductId;
    document.getElementById('contributionProductInfo').innerHTML = `
        <strong>${productTitle}</strong><br>
        <small>Doel: €${formatCurrency(targetAmount)} | Nog te verzamelen: €${formatCurrency(remaining)}</small>
    `;
    document.getElementById('remainingAmount').textContent = `Maximaal €${formatCurrency(remaining)} kan nog worden bijgedragen`;
    // Remove max restriction and let backend handle validation
    document.getElementById('contribution_amount').removeAttribute('max');
    
    // Reset form
    document.getElementById('contributionForm').reset();
    document.getElementById('contribution_list_product_id').value = listProductId;
    document.getElementById('contributionError').classList.add('d-none');
    document.getElementById('contributionSuccess').classList.add('d-none');
    
    contributionModal.show();
}

function submitContribution() {
    const form = document.getElementById('contributionForm');
    const formData = new FormData(form);
    
    // Normalize amount (replace comma with dot for proper parsing)
    let amountValue = formData.get('amount');
    if (amountValue) {
        amountValue = amountValue.replace(',', '.');
        formData.set('amount', amountValue);
    }
    
    // Validate
    const name = formData.get('contributor_name');
    const amount = parseFloat(amountValue);
    
    if (!name || !amount || amount <= 0) {
        showContributionError('Vul alle verplichte velden in');
        return;
    }
    
    // Disable button
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verwerken...';
    
    fetch('<?= base_url('contribution/add') ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-heart"></i> Bijdragen';
        
        if (data.success) {
            showContributionSuccess(data.message);
            
            // Reload page after 2 seconds to show updated stats
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showContributionError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-heart"></i> Bijdragen';
        showContributionError('Er is een fout opgetreden. Probeer het opnieuw.');
    });
}

function showContributionError(message) {
    const errorDiv = document.getElementById('contributionError');
    errorDiv.textContent = message;
    errorDiv.classList.remove('d-none');
    document.getElementById('contributionSuccess').classList.add('d-none');
}

function showContributionSuccess(message) {
    const successDiv = document.getElementById('contributionSuccess');
    successDiv.textContent = message;
    successDiv.classList.remove('d-none');
    document.getElementById('contributionError').classList.add('d-none');
}
</script>
