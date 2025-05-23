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
            'paged' => 1,
        ], $atts);

        // Get all years for selector
        $years = get_posts([
            'post_type' => 'photo_gallery',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        $year_options = [];
        foreach ($years as $pid) {
            $y = get_post_meta($pid, '_pg_year', true);
            if ($y) $year_options[$y] = $y;
        }
        krsort($year_options);

        $args = [
            'post_type' => 'photo_gallery',
            'posts_per_page' => intval($atts['per_page']),
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => intval($atts['paged']),
        ];
        if (!empty($atts['year'])) {
            $args['meta_query'] = [[
                'key' => '_pg_year',
                'value' => intval($atts['year']),
                'compare' => '=',
            ]];
        }

        $q = new WP_Query($args);
        $total_pages = $q->max_num_pages;
        ob_start();
        ?>
        <div class="nai-pg-gallery-archive">
            <div class="nai-pg-gallery-header">
                <h2 class="nai-pg-gallery-title"><?php echo __('Фотогалереи', 'salient-child'); ?> <span class="nai-pg-gallery-year-select-wrap">
                    <select class="nai-pg-gallery-year-select">
                        <option value=""><?php echo __('Все', 'salient-child'); ?></option>
                        <?php foreach($year_options as $y): ?>
                            <option value="<?php echo esc_attr($y); ?>"<?php if($atts['year']==$y)echo ' selected';?>><?php echo esc_html($y); ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>
                </h2>
            </div>
            <div class="nai-pg-vc-grid nai-pg-gallery-list" style="display:grid;grid-template-columns:repeat(<?php echo intval($atts['columns']); ?>,1fr);gap:32px;">
                <?php
                if ($q->have_posts()):
                    while ($q->have_posts()): $q->the_post();
                        $year = get_post_meta(get_the_ID(), '_pg_year', true);
                        $images = get_post_meta(get_the_ID(), '_pg_images', true);
                        $img_count = is_array($images) ? count($images) : 0;
                        $cover = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        if (!$cover && $img_count) $cover = wp_get_attachment_image_url($images[0], 'large');
                        $date = get_the_date('d.m.Y');
                        ?>
                        <a href="<?php the_permalink(); ?>" class="pg-archive-card">
                            <div class="pg-archive-card-img" style="background-image:url('<?php echo esc_url($cover); ?>');height:270px;">
                                <div class="pg-archive-card-overlay"></div>
                                <div class="pg-archive-card-info">
                                    <div class="pg-archive-card-meta">
                                        <span class="pg-archive-card-date"><svg width="18" height="18" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?php echo esc_html($year); ?></span>
                                        <span class="pg-archive-card-count"><svg width="18" height="18" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10.5" r="1.5"/><path d="M21 19l-5.5-7-4.5 6-3-4-4 5"/></svg> <?php echo esc_html($img_count); ?> <?php echo __('фото', 'salient-child'); ?></span>
                                    </div>
                                    <div class="pg-archive-card-title"><?php the_title(); ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endwhile;
                else: ?>
                    <p><?php _e('Галереи не найдены.', 'salient-child'); ?></p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
            <?php if($total_pages>1): ?>
            <div class="nai-pg-gallery-pagination">
                <?php echo paginate_links([
                    'total' => $total_pages,
                    'current' => intval($atts['paged']),
                    'format' => '#',
                    'prev_text' => __('&lt;', 'salient-child'),
                    'next_text' => __('&gt;', 'salient-child'),
                ]); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
new NAI_Photo_Gallery_Widget(); 