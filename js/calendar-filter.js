document.addEventListener('DOMContentLoaded', function () {
    const filterForms = document.querySelectorAll('.main-filter form, .sub-filter form');
    const ajaxResultsContainer = document.querySelector('#list-events');

    // Serialize all inputs into query string
    function getFormData() {
        const params = new URLSearchParams();

        filterForms.forEach(form => {
            const elements = form.querySelectorAll('input, select, textarea');

            elements.forEach(el => {
                if (!el.name) return; // skip unnamed inputs

                if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;

                params.append(el.name, el.value);
            });
        });

        return params.toString();
    }


    // AJAX request to fetch posts
    function fetchFilteredPosts() {
        const queryString = getFormData();

        fetch(calendar_ajax_object.ajax_url + '?action=filter_posts&' + queryString, {
            method: 'GET',
        })
        .then(response => response.text())
        .then(data => {
            if (ajaxResultsContainer) {
                ajaxResultsContainer.innerHTML = data;
            } else {
                console.warn('#ajax-results container not found.');
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
        });
    }

    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    // Attach listeners to inputs, checkboxes, search boxes
    filterForms.forEach(form => {
        form.addEventListener('change', () => {
            fetchFilteredPosts();
        });

        // Search box live input (keyup)
        const searchInputs = form.querySelectorAll('input[type="text"]');
        const debouncedFetch = debounce(fetchFilteredPosts, 300);
        searchInputs.forEach(input => {
            input.addEventListener('keyup', debouncedFetch);
        });

        // Reset button
        const resetBtn = form.querySelector('input[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                setTimeout(() => {
                    fetchFilteredPosts();
                }, 100); // wait for reset to apply
            });
        }

        // Clear all buttons inside dropdowns
        const clearBtns = form.querySelectorAll('.clear-btn');
        clearBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const parent = btn.closest('.dropdown-menu');
                const checkboxes = parent.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                fetchFilteredPosts();
            });
        });
    });
});