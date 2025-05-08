<?php
/**
 * Register Custom Post Type: Opinions
 * @package NAI_Theme
 */

if (!defined('ABSPATH')) exit;

class NAI_Opinions_Post_Type {
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function register_post_type() {
        $labels = [
            'name'               => __('Мнения и оценки', 'salient-child'),
            'singular_name'      => __('Мнение', 'salient-child'),
            'menu_name'          => __('Мнения и оценки', 'salient-child'),
            'add_new'            => __('Добавить', 'salient-child'),
            'add_new_item'       => __('Добавить мнение', 'salient-child'),
            'edit_item'          => __('Редактировать мнение', 'salient-child'),
            'new_item'           => __('Новое мнение', 'salient-child'),
            'view_item'          => __('Просмотреть мнение', 'salient-child'),
            'all_items'          => __('Все мнения', 'salient-child'),
            'search_items'       => __('Поиск мнений', 'salient-child'),
            'not_found'          => __('Мнения не найдены.', 'salient-child'),
            'not_found_in_trash' => __('В корзине мнений не найдено.', 'salient-child')
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-format-quote',
            'supports'           => ['title', 'editor', 'excerpt', 'thumbnail'],
            'has_archive'        => true,
        ];

        register_post_type('nai_opinion', $args);
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            wp_enqueue_media();
            wp_enqueue_script('nai-opinion-media', get_stylesheet_directory_uri() . '/assets/js/nai-opinion-media.js', ['jquery'], null, true);
        }
    }

    public function add_meta_boxes() {
        add_meta_box(
            'nai_opinion_details',
            __('Детали автора', 'salient-child'),
            [$this, 'render_meta_box'],
            'nai_opinion',
            'side',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('nai_save_opinion_details', 'nai_opinion_details_nonce');
        $author_name = get_post_meta($post->ID, '_nai_opinion_author_name', true);
        $author_position = get_post_meta($post->ID, '_nai_opinion_author_position', true);
        $author_photo_id = get_post_meta($post->ID, '_nai_opinion_author_photo_id', true);
        $author_photo_url = $author_photo_id ? wp_get_attachment_image_url($author_photo_id, 'thumbnail') : '';
        $opinion_date = get_post_meta($post->ID, '_nai_opinion_date', true);
        ?>
        <p>
            <label for="nai_opinion_author_name"><strong><?php esc_html_e('Имя автора', 'salient-child'); ?>:</strong></label><br>
            <input type="text" id="nai_opinion_author_name" name="nai_opinion_author_name" value="<?php echo esc_attr($author_name); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="nai_opinion_author_position"><strong><?php esc_html_e('Должность автора', 'salient-child'); ?>:</strong></label><br>
            <input type="text" id="nai_opinion_author_position" name="nai_opinion_author_position" value="<?php echo esc_attr($author_position); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="nai_opinion_author_photo_id"><strong><?php esc_html_e('Фото автора', 'salient-child'); ?>:</strong></label><br>
            <input type="hidden" id="nai_opinion_author_photo_id" name="nai_opinion_author_photo_id" value="<?php echo esc_attr($author_photo_id); ?>" />
            <img id="nai_opinion_author_photo_preview" src="<?php echo esc_url($author_photo_url); ?>" style="max-width:100%;<?php echo $author_photo_url ? '' : 'display:none;'; ?>" />
            <br>
            <button type="button" class="button" id="nai_opinion_author_photo_button"><?php esc_html_e('Выбрать фото', 'salient-child'); ?></button>
        </p>
       
        <?php
    }

    public function save_meta_boxes($post_id) {
        if (!isset($_POST['nai_opinion_details_nonce']) || !wp_verify_nonce($_POST['nai_opinion_details_nonce'], 'nai_save_opinion_details')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = [
            'nai_opinion_author_name',
            'nai_opinion_author_position',
            'nai_opinion_author_photo_id',
          
        ];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}
new NAI_Opinions_Post_Type();
