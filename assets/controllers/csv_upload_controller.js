import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static values = { maxSizeMB: Number }

    validateForm(event) {
        const fileInput = this.element.querySelector('#csv_file')
        const file = fileInput.files[0]
        
        if (!file) {
            alert('Please select a file')
            event.preventDefault()
            return
        }
        
        if (!file.name.toLowerCase().endsWith('.csv')) {
            alert('Please select a CSV file')
            event.preventDefault()
            return
        }
        
        if (file.size > this.maxSizeMBValue * 1024 * 1024) {
            alert(`File size must be less than ${this.maxSizeMBValue}MB`)
            event.preventDefault()
            return
        }
    }
}