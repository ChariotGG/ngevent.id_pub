/**
 * Event Search Page JS
 * Filter, sort, AJAX pagination
 */

import api from '../utils/api';

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initSort();
    initLoadMore();
});

function initFilters() {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;

    // Category filter
    const categoryInputs = filterForm.querySelectorAll('input[name="category"]');
    categoryInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
    });

    // Date filter
    const dateInput = filterForm.querySelector('input[name="date"]');
    if (dateInput) {
        dateInput.addEventListener('change', applyFilters);
    }

    // Location filter
    const locationInput = filterForm.querySelector('input[name="location"]');
    if (locationInput) {
        locationInput.addEventListener('change', applyFilters);
    }

    // Reset filter
    const resetBtn = document.getElementById('resetFilter');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }
}

function initSort() {
    const sortSelect = document.getElementById('sortBy');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', applyFilters);
}

function applyFilters() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);

    window.location.href = window.location.pathname + '?' + params.toString();
}

function resetFilters() {
    window.location.href = window.location.pathname;
}

function initLoadMore() {
    const loadMoreBtn = document.getElementById('loadMore');
    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', async () => {
        const page = parseInt(loadMoreBtn.dataset.page) + 1;

        try {
            loadMoreBtn.textContent = 'Loading...';
            loadMoreBtn.disabled = true;

            const response = await api.get('/events?page=' + page);

            // Append events to grid
            const grid = document.getElementById('eventsGrid');
            grid.insertAdjacentHTML('beforeend', response.html);

            loadMoreBtn.dataset.page = page;
            loadMoreBtn.textContent = 'Load More';
            loadMoreBtn.disabled = false;

            if (!response.has_more) {
                loadMoreBtn.remove();
            }

        } catch (error) {
            console.error('Load more error:', error);
            loadMoreBtn.textContent = 'Load More';
            loadMoreBtn.disabled = false;
        }
    });
}

console.log('🔍 Event Search loaded');
