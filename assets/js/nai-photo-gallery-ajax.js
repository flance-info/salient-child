document.addEventListener('DOMContentLoaded', function() {
    const wrap = document.querySelector('.pg-single-gallery-wrap');
    if (!wrap) return;
    const postId = wrap.getAttribute('data-post-id');

    wrap.addEventListener('click', function(e) {
        if (e.target.closest('.pg-single-pagination a')) {
            e.preventDefault();
            const link = e.target.closest('.pg-single-pagination a');
            const paged = parseInt(link.textContent) || (link.textContent.includes('›') ? 2 : 1);
            fetchGalleryPage(paged);
        }
    });

    function fetchGalleryPage(paged) {
        const data = new FormData();
        data.append('action', 'nai_gallery_pagination');
        data.append('post_id', postId);
        data.append('paged', paged);

        fetch(window.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            wrap.querySelector('.pg-single-gallery').innerHTML = res.gallery;
            const pagDiv = wrap.querySelector('.pg-single-pagination');
            if (pagDiv) pagDiv.outerHTML = res.pagination;
            else wrap.insertAdjacentHTML('beforeend', res.pagination);
            if (window.Fancybox) Fancybox.bind('[data-fancybox=gallery]');
        });
    }
});
