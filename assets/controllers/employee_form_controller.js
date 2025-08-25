import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    validateForm(event) {
        const inputs = this.element.querySelectorAll('input[required]')
        let valid = true
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid')
                valid = false
            } else {
                input.classList.remove('is-invalid')
            }
        })
        
        const email = this.element.querySelector('#email_address')
        if (email.value && !email.validity.valid) {
            email.classList.add('is-invalid')
            valid = false
        }
        
        const salary = this.element.querySelector('#salary')
        if (salary.value && parseFloat(salary.value) < 0) {
            salary.classList.add('is-invalid')
            valid = false
        }
        
        if (!valid) {
            event.preventDefault()
        }
    }
}