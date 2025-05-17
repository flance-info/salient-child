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
            <div class="analytics-materials-list">
                <?php while ($q->have_posts()): $q->the_post();
                    $file_id = get_post_meta(get_the_ID(), '_analytics_material_file', true);
                    $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
                    $cats = get_the_terms(get_the_ID(), 'analytics_category');
                    ?>
                    <div class="analytics-material-card">
                        <?php if ($cats): ?>
                            <div class="analytics-material-category"><?php echo esc_html($cats[0]->name); ?></div>
                        <?php endif; ?>
                        <div class="analytics-material-title"><?php the_title(); ?></div>
                        <div class="analytics-material-date"><?php echo get_the_date('Y'); ?></div>
                        <?php if ($file_url): ?>
                            <a href="<?php echo esc_url($file_url); ?>" class="analytics-material-download" download>Скачать</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
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