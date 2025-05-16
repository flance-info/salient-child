(function($){
    function openMediaFrame($field) {
        var frame = wp.media({
            title: 'Select or Upload PDF/PPTX',
            button: { text: 'Use this file' },
            multiple: false,
            library: { type: ['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'] }
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $field.find('input').val(attachment.id).trigger('change');
            $field.find('.nai-media-file-preview').text(attachment.filename);
            $field.find('.nai-media-file-remove').show();
        });
        frame.open();
    }
    $(document).on('click', '.nai-media-file-upload', function(e){
        e.preventDefault();
        openMediaFrame($(this).closest('.nai-media-file-field'));
    });
    $(document).on('click', '.nai-media-file-remove', function(e){
        e.preventDefault();
        var $field = $(this).closest('.nai-media-file-field');
        $field.find('input').val('');
        $field.find('.nai-media-file-preview').text('');
        $(this).hide();
    });
})(window.jQuery); 