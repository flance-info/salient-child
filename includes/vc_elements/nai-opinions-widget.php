<?php
/**
 * NAI Opinions Widget for Visual Composer
 */
if (!defined('ABSPATH')) exit;

if (!class_exists('NAI_Opinions_Widget')) {
    class NAI_Opinions_Widget {
        public function __construct() {
            add_action('vc_before_init', [$this, 'integrate_with_vc']);
            add_shortcode('nai_opinions', [$this, 'render_opinions_widget']);
        }

        public function integrate_with_vc() {
            vc_map([
                'name' => esc_html__('NAI Opinions', 'salient-child'),
                'description' => esc_html__('Display opinions in a list', 'salient-child'),
                'base' => 'nai_opinions',
                'category' => esc_html__('NAI Elements', 'salient-child'),
                'icon' => 'vc_icon-vc-masonry-grid',
                'params' => [
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Number of Opinions', 'salient-child'),
                        'param_name' => 'number_of_opinions',
                        'value' => '5',
                        'description' => esc_html__('Number of opinions to display', 'salient-child'),
                    ],
                ],
            ]);
        }

        public function render_opinions_widget($atts) {
            $atts = shortcode_atts([
                'number_of_opinions' => 5,
            ], $atts);

            $args = [
                'post_type' => 'nai_opinion',
                'posts_per_page' => intval($atts['number_of_opinions']),
                'orderby' => 'date',
                'order' => 'DESC',
            ];
            $opinions = new WP_Query($args);

            ob_start();
            $template_path = locate_template('vc_elements/opinions-widget-view.php');
            
            if (file_exists($template_path)) {
                include $template_path;
            }
            return ob_get_clean();
        }
    }
    new NAI_Opinions_Widget();
}
