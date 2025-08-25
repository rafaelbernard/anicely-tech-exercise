import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["emailDisplay", "emailEdit", "editButton", "saveButton", "cancelButton"]

    editEmail(event) {
        const row = event.target.closest('tr')
        this.toggleEditMode(row, true)
    }

    saveEmail(event) {
        const row = event.target.closest('tr')
        const email = row.querySelector('.email-edit').value
        const id = event.target.dataset.id

        fetch(`/employees/${id}/update-email`, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `email=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.querySelector('.email-display').textContent = email
                this.toggleEditMode(row, false)
                this.showAlert('Email updated successfully', 'success')
            } else {
                this.showAlert('Error: ' + data.error, 'danger')
            }
        })
    }

    cancelEdit(event) {
        const row = event.target.closest('tr')
        this.toggleEditMode(row, false)
    }

    toggleEditMode(row, isEditing) {
        row.querySelector('.email-display').classList.toggle('d-none', isEditing)
        row.querySelector('.email-edit').classList.toggle('d-none', !isEditing)
        row.querySelector('.edit-email').classList.toggle('d-none', isEditing)
        row.querySelector('.save-email').classList.toggle('d-none', !isEditing)
        row.querySelector('.cancel-edit').classList.toggle('d-none', !isEditing)
    }

    showAlert(message, type) {
        const alertDiv = document.createElement('div')
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `
        document.querySelector('.container').insertBefore(alertDiv, document.querySelector('h1').nextSibling)
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove()
            }
        }, 3000)
    }
}