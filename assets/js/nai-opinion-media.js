jQuery(document).ready(function($){
    var frame;
    $('#nai_opinion_author_photo_button').on('click', function(e){
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Выбрать фото',
            button: { text: 'Использовать фото' },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#nai_opinion_author_photo_id').val(attachment.id);
            $('#nai_opinion_author_photo_preview').attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).show();
        });
        frame.open();
    });
}); 