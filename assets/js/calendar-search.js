document.addEventListener('DOMContentLoaded', function() {
    // Handle search in dropdown menus
    const searchBoxes = document.querySelectorAll('.search-box');
    
    searchBoxes.forEach(searchBox => {
        searchBox.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const dropdown = this.closest('.dropdown-menu');
            const items = dropdown.querySelectorAll('.dropdown-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Handle main keyword search
    const keywordInput = document.querySelector('.input-search input');
    if (keywordInput) {
        keywordInput.addEventListener('input', debounce(function() {
            filterEvents();
        }, 300));
    }
    
    // Filter events based on all criteria
    function filterEvents() {
        // Get all selected filters
        const selectedPartners = getSelectedValues('partner');
        const selectedSpeakers = getSelectedValues('speakers');
        const selectedThemes = getSelectedValues('event_theme');
        const selectedRegions = getSelectedValues('region');
        const keyword = keywordInput ? keywordInput.value.toLowerCase() : '';
        
        // Apply filters to events (you'll need to implement this based on your events display)
        console.log('Filtering with:', {
            partners: selectedPartners,
            speakers: selectedSpeakers,
            themes: selectedThemes,
            regions: selectedRegions,
            keyword: keyword
        });
    }
    
    function getSelectedValues(type) {
        const checkboxes = document.querySelectorAll(`input[type="checkbox"]:checked`);
        return Array.from(checkboxes).map(cb => cb.value);
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});