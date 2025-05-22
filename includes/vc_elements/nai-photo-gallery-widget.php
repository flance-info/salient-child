<?php
if (!defined('ABSPATH')) exit;

class NAI_Photo_Gallery_Widget {
    public function __construct() {
        add_action('vc_before_init', [$this, 'integrate_with_vc']);
        add_shortcode('nai_photo_gallery', [$this, 'render_widget']);
    }

    public function integrate_with_vc() {
        vc_map([
            'name' => 'NAI Photo Gallery',
            'base' => 'nai_photo_gallery',
            'category' => 'NAI Elements',
            'icon' => 'vc_icon-vc-media-grid',
            'params' => [
                [
                    'type' => 'textfield',
                    'heading' => 'Year (optional)',
                    'param_name' => 'year',
                    'description' => 'Show only galleries from this year (leave blank for all years)',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Items per page',
                    'param_name' => 'per_page',
                    'value' => 6,
                ],
                [
                    'type' => 'dropdown',
                    'heading' => 'Columns',
                    'param_name' => 'columns',
                    'value' => [1=>1, 2=>2, 3=>3],
                    'std' => 3,
                ],
            ],
        ]);
    }

    public function render_widget($atts) {
        $atts = shortcode_atts([
            'year' => '',
            'per_page' => 6,
            'columns' => 3,
        ], $atts);

        $args = [
            'post_type' => 'photo_gallery',
            'posts_per_page' => intval($atts['per_page']),
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        if (!empty($atts['year'])) {
            $args['meta_query'] = [[
                'key' => '_pg_year',
                'value' => intval($atts['year']),
                'compare' => '=',
            ]];
        }

        $q = new WP_Query($args);
        ob_start();
        ?>
        <div class="nai-pg-vc-grid" style="display:grid;grid-template-columns:repeat(<?php echo intval($atts['columns']); ?>,1fr);gap:32px;">
            <?php
            if ($q->have_posts()):
                while ($q->have_posts()): $q->the_post();
                    $year = get_post_meta(get_the_ID(), '_pg_year', true);
                    $images = get_post_meta(get_the_ID(), '_pg_images', true);
                    $img_count = is_array($images) ? count($images) : 0;
                    $cover = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$cover && $img_count) $cover = wp_get_attachment_image_url($images[0], 'large');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="pg-archive-card">
                        <div class="pg-archive-card-img" style="background-image:url('<?php echo esc_url($cover); ?>')">
                            <div class="pg-archive-card-overlay"></div>
                            <div class="pg-archive-card-meta">
                                <span class="pg-archive-card-date"><?php echo esc_html($year); ?></span>
                                <span class="pg-archive-card-count"><?php echo esc_html($img_count); ?> фото</span>
                            </div>
                            <div class="pg-archive-card-title"><?php the_title(); ?></div>
                        </div>
                    </a>
                <?php endwhile;
            else: ?>
                <p>Галереи не найдены.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
new NAI_Photo_Gallery_Widget(); 