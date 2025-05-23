jQuery(document).ready(function($) {
    var archive = $('.nai-pg-gallery-archive');
    if (!archive.length) return;
    var yearSelect = archive.find('.nai-pg-gallery-year-select');

    function fetchGallery(year, paged) {
        var data = new FormData();
        data.append('action', 'nai_gallery_archive');
        data.append('year', year || '');
        data.append('paged', paged || 1);
        fetch(window.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if (res.html) {
                archive.html(res.html);
            }
        });
    }

    // Pagination click
    archive.on('click', '.nai-pg-gallery-pagination a', function(e) {
        e.preventDefault();
        var paged = parseInt($(this).text()) || 1;
        var year = $('.nai-pg-gallery-year').text();
        fetchGallery(year, paged);
    });

    // Toggle year dropdown
    $(document).on('click', '.nai-pg-gallery-year, .nai-pg-gallery-year-dropdown', function(e) {
        e.stopPropagation();
        $('.nai-pg-gallery-year-select').toggleClass('open');
    });

    // Select year
    $(document).on('click', '.nai-pg-gallery-year-options a', function(e) {
        e.preventDefault();
        var year = $(this).data('year');
        $('.nai-pg-gallery-year').text(year);
        $('.nai-pg-gallery-year-select').removeClass('open');
        fetchGallery(year, 1);
    });

    // Close dropdown on outside click
    $(document).on('click', function() {
        $('.nai-pg-gallery-year-select').removeClass('open');
    });
}); 



