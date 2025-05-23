document.addEventListener('DOMContentLoaded', function() {
    const wrap = document.querySelector('.pg-single-gallery-wrap');
    const container = document.querySelector('.pg-single-container');
    if (!wrap || !container) return;
    const postId = wrap.getAttribute('data-post-id');
    const loader = container.querySelector('.pg-gallery-loader');

    wrap.addEventListener('click', function(e) {
        if (e.target.closest('.pg-single-pagination a')) {
            e.preventDefault();
            const link = e.target.closest('.pg-single-pagination a');
            const paged = parseInt(link.textContent) || (link.textContent.includes('›') ? 2 : 1);
            fetchGalleryPage(paged);
        }
    });

    function fetchGalleryPage(paged) {
        // Fade out gallery and show loader
        wrap.querySelector('.pg-single-gallery').classList.add('loading');
        if (wrap.querySelector('.pg-single-pagination')) {
            wrap.querySelector('.pg-single-pagination').classList.add('loading');
        }
        loader.style.display = 'flex';

        const data = new FormData();
        data.append('action', 'nai_gallery_pagination');
        data.append('post_id', postId);
        data.append('paged', paged);

        setTimeout(() => { // Slow down for effect
            fetch(window.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                // Fade in new content
                wrap.querySelector('.pg-single-gallery').innerHTML = res.gallery;
                const pagDiv = wrap.querySelector('.pg-single-pagination');
                if (pagDiv) pagDiv.outerHTML = res.pagination;
                else wrap.insertAdjacentHTML('beforeend', res.pagination);

                setTimeout(() => {
                    wrap.querySelector('.pg-single-gallery').classList.remove('loading');
                    if (wrap.querySelector('.pg-single-pagination')) {
                        wrap.querySelector('.pg-single-pagination').classList.remove('loading');
                    }
                    loader.style.display = 'none';
                    if (window.Fancybox) Fancybox.bind('[data-fancybox=gallery]');
                    // Scroll to gallery container
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 350); // Fade in after a short delay
            });
        }, 350); // Slow transition
    }
});


