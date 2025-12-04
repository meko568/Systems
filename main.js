/**
 * System Management - Client-side interactions
 * Handles: Delete confirmations, form interactivity, animations
 */

document.addEventListener('DOMContentLoaded', function() {
    addDeleteConfirmations();
    autoCloseAlerts();
});

/**
 * Add confirmation dialog before deleting a system
 */
function addDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('button[type="submit"][class*="btn-danger"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const systemName = this.closest('.card').querySelector('.card-title').textContent;
            
            if (!confirm(`Are you sure you want to delete "${systemName}"? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Auto-dismiss alert messages after 5 seconds
 */
function autoCloseAlerts() {
    const alerts = document.querySelectorAll('.alert-warning');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}
