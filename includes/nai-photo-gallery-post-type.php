<?php
if (!defined('ABSPATH')) exit;

// Register Custom Post Type
add_action('init', function() {
    register_post_type('photo_gallery', [
        'labels' => [
            'name' => 'Фотогалереи',
            'singular_name' => 'Фотогалерея',
            'add_new' => 'Добавить новую',
            'add_new_item' => 'Добавить новую фотогалерею',
            'edit_item' => 'Редактировать фотогалерею',
            'new_item' => 'Новая фотогалерея',
            'view_item' => 'Просмотреть фотогалерею',
            'search_items' => 'Искать фотогалерею',
            'not_found' => 'Не найдено',
            'not_found_in_trash' => 'В корзине не найдено',
            'all_items' => 'Все фотогалереи',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'photo-galleries'],
        'menu_icon' => 'dashicons-format-gallery',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
});

// Add Meta Boxes
add_action('add_meta_boxes', function() {
    add_meta_box('pg_year', 'Год', 'nai_pg_year_box', 'photo_gallery', 'side');
    add_meta_box('pg_images', 'Фотографии галереи', 'nai_pg_images_box', 'photo_gallery', 'normal', 'default');
});

// Year Field
function nai_pg_year_box($post) {
    $year = get_post_meta($post->ID, '_pg_year', true);
    echo '<input type="number" name="pg_year" value="' . esc_attr($year ? $year : date('Y')) . '" min="2000" max="2100" style="width:100%">';
}

// Gallery Images Field
function nai_pg_images_box($post) {
    $image_ids = get_post_meta($post->ID, '_pg_images', true);
    if (!is_array($image_ids)) $image_ids = [];
    ?>
    <div id="pg-images-list">
        <?php foreach ($image_ids as $img_id): 
            $img_url = wp_get_attachment_image_url($img_id, 'medium');
            ?>
            <div class="pg-image-item" style="display:inline-block;margin:5px;position:relative;">
                <img src="<?php echo esc_url($img_url); ?>" style="max-width:120px;">
                <input type="hidden" name="pg_images[]" value="<?php echo esc_attr($img_id); ?>">
                <button type="button" class="button pg-remove-image" style="position:absolute;top:0;right:0;">×</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" id="pg-add-image">Добавить фото</button>
    <script>
    jQuery(function($){
        $('#pg-add-image').on('click', function(e){
            e.preventDefault();
            var frame = wp.media({title:'Выбрать фото', multiple:true, library:{type:'image'}});
            frame.on('select', function(){
                var selection = frame.state().get('selection');
                selection.each(function(attachment){
                    var img_id = attachment.id, img_url = attachment.attributes.sizes.medium ? attachment.attributes.sizes.medium.url : attachment.attributes.url;
                    $('#pg-images-list').append(
                        '<div class="pg-image-item" style="display:inline-block;margin:5px;position:relative;">'+
                        '<img src="'+img_url+'" style="max-width:120px;">'+
                        '<input type="hidden" name="pg_images[]" value="'+img_id+'">'+
                        '<button type="button" class="button pg-remove-image" style="position:absolute;top:0;right:0;">×</button>'+
                        '</div>'
                    );
                });
            });
            frame.open();
        });
        $(document).on('click', '.pg-remove-image', function(){
            $(this).closest('.pg-image-item').remove();
        });
    });
    </script>
    <?php
}

// Save Meta Fields
add_action('save_post_photo_gallery', function($post_id){
    if (isset($_POST['pg_year'])) {
        update_post_meta($post_id, '_pg_year', intval($_POST['pg_year']));
    }
    if (isset($_POST['pg_images'])) {
        $imgs = array_map('intval', $_POST['pg_images']);
        update_post_meta($post_id, '_pg_images', $imgs);
    } else {
        delete_post_meta($post_id, '_pg_images');
    }
}); 