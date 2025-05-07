jQuery(document).ready(function($) {
    $('.nai-read-more-link').on('click', function(e) {
        e.preventDefault();
        $(this).siblings('.nai-read-more-content').slideToggle();
        $(this).text($(this).text() === 'Подробнее' ? 'Скрыть' : 'Подробнее');
    });
});