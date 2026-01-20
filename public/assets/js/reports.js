/**
 * Report Generation Form - Dynamic field toggling
 */

document.addEventListener('DOMContentLoaded', function() {
    const reportTypeSelect = document.getElementById('reportType');
    const monthSelection = document.getElementById('monthSelection');
    
    if (reportTypeSelect && monthSelection) {
        // Hide/show month selection based on report type
        reportTypeSelect.addEventListener('change', function() {
            if (this.value === 'monthly') {
                monthSelection.style.display = 'block';
            } else {
                monthSelection.style.display = 'none';
            }
        });
        
        // Initialize: if yearly is selected, hide month selection
        if (reportTypeSelect.value !== 'monthly') {
            monthSelection.style.display = 'none';
        }
    }
});
