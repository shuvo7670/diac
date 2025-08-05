(function ($) {
  "use strict";

  document
    .querySelectorAll('.sub-filter input[type="checkbox"]')
    .forEach(function (checkbox) {
      checkbox.addEventListener("change", function () {
        if (this.checked) {
          this.parentElement.classList.add("checked");
        } else {
          this.parentElement.classList.remove("checked");
        }
      });
    });



 document.querySelectorAll('.dropdown').forEach(dropdown => {
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const label = toggle.querySelector('.label');
    const menu = dropdown.querySelector('.dropdown-menu');
    const checkboxes = dropdown.querySelectorAll('.item-checkbox');
    const searchBox = dropdown.querySelector('.search-box');
    const clearBtn = dropdown.querySelector('.clear-btn');
    const items = dropdown.querySelectorAll('.dropdown-item');

    // Toggle menu
    toggle.addEventListener('click', () => {
      document.querySelectorAll('.dropdown-menu').forEach(m => {
        if (m !== menu) m.classList.remove('show');
      });
      menu.classList.toggle('show');
    });

    // Click on item toggles checkbox
    items.forEach(item => {
      item.addEventListener('click', (e) => {
        const checkbox = item.querySelector('.item-checkbox');
        if (e.target !== checkbox) {
          checkbox.checked = !checkbox.checked;
        }
        updateSelectedCount();
      });
    });

    // Update label text
    function updateSelectedCount() {
      const selectedCount = dropdown.querySelectorAll('.item-checkbox:checked').length;
      label.textContent = selectedCount > 0 ? `${selectedCount} selected` : 'Partner';
    }

    // Filter items
    searchBox.addEventListener('keyup', () => {
      const filter = searchBox.value.toLowerCase();
      items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? '' : 'none';
      });
    });

    // Clear all
    clearBtn.addEventListener('click', () => {
      checkboxes.forEach(cb => cb.checked = false);
      updateSelectedCount();
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target)) {
        menu.classList.remove('show');
      }
    });
  });


//   
document.addEventListener("DOMContentLoaded", function () {
    const timelineItems = document.querySelectorAll(".timeline li");
    const listEvents = document.querySelector(".list-events");

    // Remove all current classes
    function clearCurrentClass() {
        timelineItems.forEach(item => item.classList.remove("current"));
    }

    // Scroll list-events container
    timelineItems.forEach(item => {
        item.addEventListener("click", function () {
            const date = this.getAttribute("data-date");
            const target = document.getElementById("event-" + date);

            if (target && listEvents) {
                const listEventsTop = listEvents.getBoundingClientRect().top;
                const targetTop = target.getBoundingClientRect().top;
                const scrollOffset = targetTop - listEventsTop + listEvents.scrollTop;

                listEvents.scrollTo({
                    top: scrollOffset,
                    behavior: "smooth"
                });

                clearCurrentClass();
                this.classList.add("current");
            }
        });
    });

    // Default: highlight last timeline item
    const latest = document.querySelector(".timeline li:last-child");
    if (latest) {
        clearCurrentClass();
        latest.classList.add("current");
    }
});
})(jQuery);
