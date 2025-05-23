document.addEventListener('DOMContentLoaded', function() {
    const archive = document.querySelector('.nai-pg-gallery-archive');
    if (!archive) return;
    const grid = archive.querySelector('.nai-pg-gallery-list');
    const yearSelect = archive.querySelector('.nai-pg-gallery-year-select');
    const pagination = archive.querySelector('.nai-pg-gallery-pagination');

    function fetchGallery(year, paged = 1) {
        const data = new FormData();
        data.append('action', 'nai_gallery_archive');
        data.append('year', year || '');
        data.append('paged', paged);
        fetch(window.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if (res.html) {
                archive.innerHTML = res.html;
            }
        });
    }

    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            fetchGallery(this.value, 1);
        });
    }

    archive.addEventListener('click', function(e) {
        if (e.target.closest('.nai-pg-gallery-pagination a')) {
            e.preventDefault();
            const link = e.target.closest('.nai-pg-gallery-pagination a');
            const paged = parseInt(link.textContent) || 1;
            const year = yearSelect ? yearSelect.value : '';
            fetchGallery(year, paged);
        }
    });
}); 