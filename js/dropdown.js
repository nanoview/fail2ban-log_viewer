// Export dropdown toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const exportDropdown = document.querySelector('.export-dropdown');
    const exportBtn = document.querySelector('.export-btn');
    
    if(exportBtn && exportDropdown) {
        // Toggle dropdown on button click
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            exportDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking on menu items
        const dropdownLinks = exportDropdown.querySelectorAll('.dropdown-menu a');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                exportDropdown.classList.remove('active');
            });
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.export-dropdown');
        if(dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
});
