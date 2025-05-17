<?php
// Register Custom Post Type
add_action('init', function() {
    register_post_type('analytics_material', [
        'labels' => [
            'name' => 'Аналитические материалы',
            'singular_name' => 'Аналитический материал',
            'add_new' => 'Добавить материал',
            'add_new_item' => 'Добавить новый материал',
            'edit_item' => 'Редактировать материал',
            'new_item' => 'Новый материал',
            'view_item' => 'Просмотреть материал',
            'search_items' => 'Поиск материалов',
            'not_found' => 'Не найдено',
            'menu_name' => 'Аналитика',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
    // Register Taxonomy
    register_taxonomy('analytics_category', 'analytics_material', [
        'labels' => [
            'name' => 'Категории аналитики',
            'singular_name' => 'Категория аналитики',
        ],
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'analytics-category'],
    ]);
});

// Add file upload meta box
add_action('add_meta_boxes', function() {
    add_meta_box('analytics_material_file', 'Файл материала', function($post) {
        $file_id = get_post_meta($post->ID, '_analytics_material_file', true);
        $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
        ?>
        <input type="hidden" name="analytics_material_file" id="analytics_material_file" value="<?php echo esc_attr($file_id); ?>">
        <button type="button" class="button" id="analytics_material_file_upload">Загрузить/выбрать файл</button>
        <span id="analytics_material_file_name"><?php echo $file_url ? basename($file_url) : ''; ?></span>
        <script>
        jQuery(function($){
            $('#analytics_material_file_upload').on('click', function(e){
                e.preventDefault();
                var frame = wp.media({title: 'Выбрать файл', button: {text: 'Использовать'}, multiple: false});
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#analytics_material_file').val(attachment.id);
                    $('#analytics_material_file_name').text(attachment.filename);
                });
                frame.open();
            });
        });
        </script>
        <?php
    }, 'analytics_material', 'side');
});
add_action('save_post', function($post_id){
    if (isset($_POST['analytics_material_file'])) {
        update_post_meta($post_id, '_analytics_material_file', intval($_POST['analytics_material_file']));
    }
}); 