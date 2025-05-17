<?php
if (!defined('ABSPATH')) exit;

class Analytics_Materials_Widget {
    public function __construct() {
        add_action('vc_before_init', [$this, 'integrate_with_vc']);
        add_shortcode('analytics_materials', [$this, 'render_widget']);
    }
    public function integrate_with_vc() {
        vc_map([
            'name' => 'Analytics Materials',
            'base' => 'analytics_materials',
            'category' => 'NAI Elements',
            'icon' => 'vc_icon-vc-media-grid',
            'params' => [
                [
                    'type' => 'dropdown',
                    'heading' => 'Category',
                    'param_name' => 'category',
                    'value' => $this->get_categories(),
                    'description' => 'Filter by category',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Items per page',
                    'param_name' => 'per_page',
                    'value' => 9,
                ],
                [
                    'type' => 'dropdown',
                    'heading' => 'Columns',
                    'param_name' => 'columns',
                    'value' => [1=>1, 2=>2, 3=>3, 4=>4],
                    'std' => 3,
                    'description' => 'Number of columns in the grid',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Card Padding (CSS)',
                    'param_name' => 'card_padding',
                    'description' => 'Padding for each card, e.g. 24px 20px',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Card Margin (CSS)',
                    'param_name' => 'card_margin',
                    'description' => 'Margin for each card, e.g. 0 0 24px 0',
                ],
                [
                    'type' => 'colorpicker',
                    'heading' => 'Card Background Color',
                    'param_name' => 'card_bg',
                    'description' => 'Background color for each card',
                ],
                [
                    'type' => 'dropdown',
                    'heading' => 'Font Family',
                    'param_name' => 'font_family',
                    'value' => [
                        'Theme Default' => '',
                        'Arial' => 'Arial, sans-serif',
                        'Georgia' => 'Georgia, serif',
                        'Tahoma' => 'Tahoma, Geneva, sans-serif',
                        'Verdana' => 'Verdana, Geneva, sans-serif',
                        'Roboto' => 'Roboto, Arial, sans-serif',
                        'Open Sans' => 'Open Sans, Arial, sans-serif',
                    ],
                    'description' => 'Font family for card content',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Font Size (px)',
                    'param_name' => 'font_size',
                    'description' => 'Font size for card content',
                ],
                [
                    'type' => 'colorpicker',
                    'heading' => 'Font Color',
                    'param_name' => 'font_color',
                    'description' => 'Font color for card content',
                ],
            ],
        ]);
    }
    private function get_categories() {
        $terms = get_terms(['taxonomy' => 'analytics_category', 'hide_empty' => false]);
        $cats = ['Все' => ''];
        foreach ($terms as $term) {
            $cats[$term->name] = $term->slug;
        }
        return $cats;
    }
    public function render_widget($atts) {
        $atts = shortcode_atts([
            'category' => '',
            'per_page' => 9,
            'columns' => 3,
            'card_padding' => '',
            'card_margin' => '',
            'card_bg' => '',
            'font_family' => '',
            'font_size' => '',
            'font_color' => '',
        ], $atts);
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $args = [
            'post_type' => 'analytics_material',
            'posts_per_page' => intval($atts['per_page']),
            'paged' => $paged,
        ];
        if ($atts['category']) {
            $args['tax_query'] = [[
                'taxonomy' => 'analytics_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            ]];
        }
        $q = new WP_Query($args);
        $columns = intval($atts['columns']);
        $card_style = '';
        if ($atts['card_padding']) $card_style .= 'padding:' . esc_attr($atts['card_padding']) . ';';
        if ($atts['card_margin']) $card_style .= 'margin:' . esc_attr($atts['card_margin']) . ';';
        if ($atts['card_bg']) $card_style .= 'background:' . esc_attr($atts['card_bg']) . ';';
        if ($atts['font_family']) $card_style .= 'font-family:' . esc_attr($atts['font_family']) . ';';
        if ($atts['font_size']) $card_style .= 'font-size:' . esc_attr($atts['font_size']) . 'px;';
        if ($atts['font_color']) $card_style .= 'color:' . esc_attr($atts['font_color']) . ';';
        ob_start();
        ?>
        <div class="analytics-materials-grid">
            <form class="analytics-materials-filter" method="get">
                <select name="analytics_category" onchange="this.form.submit()">
                    <?php foreach ($this->get_categories() as $name => $slug): ?>
                        <option value="<?php echo esc_attr($slug); ?>"<?php selected($atts['category'], $slug); ?>><?php echo esc_html($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <div class="analytics-materials-list" style="display:grid;grid-template-columns:repeat(<?php echo $columns; ?>,1fr);gap:32px;">
                <?php while ($q->have_posts()): $q->the_post();
                    $file_id = get_post_meta(get_the_ID(), '_analytics_material_file', true);
                    $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
                    $cats = get_the_terms(get_the_ID(), 'analytics_category');
                    $custom_date = get_post_meta(get_the_ID(), '_analytics_material_date', true);
                    
                    if ($file_url) {
                        echo '<a href="' . esc_url($file_url) . '" class="analytics-material-card" style="' . esc_attr($card_style) . '" download>';
                    } else {
                        echo '<div class="analytics-material-card" style="' . esc_attr($card_style) . '">';
                    }
                    if ($cats) {
                        echo '<div class="analytics-material-category">' . esc_html($cats[0]->name) . '</div>';
                    }
                    echo '<div class="analytics-material-title">' . get_the_title() . '</div>';
                    echo '<div class="analytics-material-date">' . esc_html($custom_date ? $custom_date : get_the_date('Y')) . '</div>';
                    if ($file_url) {
                        echo '</a>';
                    } else {
                        echo '</div>';
                    }
                endwhile; ?>
            </div>
            <div class="analytics-materials-pagination">
                <?php
                echo paginate_links([
                    'total' => $q->max_num_pages,
                    'current' => $paged,
                ]);
                ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
new Analytics_Materials_Widget(); 