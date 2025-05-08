<?php
/**
 * NAI Events Widget for Visual Composer
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NAI_Events_Widget')) {
    class NAI_Events_Widget {
        public function __construct() {
            add_action('vc_before_init', array($this, 'integrate_with_vc'));
            add_shortcode('nai_events', array($this, 'render_events_widget'));
        }

        public function integrate_with_vc() {
            vc_map(array(
                'name' => esc_html__('NAI Events', 'salient-child'),
                'description' => esc_html__('Display events in a grid layout', 'salient-child'),
                'base' => 'nai_events',
                'category' => esc_html__('NAI Elements', 'salient-child'),
                'icon' => 'vc_icon-vc-masonry-grid',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Number of Events', 'salient-child'),
                        'param_name' => 'number_of_events',
                        'value' => array(
                            '3' => '3',
                            '6' => '6',
                            '9' => '9',
                            '12' => '12',
                        ),
                        'std' => '6',
                        'description' => esc_html__('Select number of events to display', 'salient-child'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Order By', 'salient-child'),
                        'param_name' => 'orderby',
                        'value' => array(
                            esc_html__('Date', 'salient-child') => 'date',
                            esc_html__('Title', 'salient-child') => 'title',
                            esc_html__('Event Date', 'salient-child') => 'event_date',
                        ),
                        'std' => 'event_date',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Order', 'salient-child'),
                        'param_name' => 'order',
                        'value' => array(
                            esc_html__('Ascending', 'salient-child') => 'ASC',
                            esc_html__('Descending', 'salient-child') => 'DESC',
                        ),
                        'std' => 'ASC',
                    ),
                ),
            ));
        }

        public function render_events_widget($atts) {
            $atts = shortcode_atts(array(
                'number_of_events' => '6',
                'orderby' => 'event_date',
                'order' => 'ASC',
            ), $atts);

            $args = array(
                'post_type' => 'nai_event',
                'posts_per_page' => intval($atts['number_of_events']),
                'order' => $atts['order'],
            );

            if ($atts['orderby'] === 'event_date') {
                $args['meta_key'] = '_nai_event_date';
                $args['orderby'] = 'meta_value';
                $args['meta_type'] = 'DATE';
            } else {
                $args['orderby'] = $atts['orderby'];
            }

            $events = new WP_Query($args);
            
            // Get the template path
            $template_path = locate_template('vc_elements/events-widget-view.php');
            
            // If template not found in theme, use the default one
            if (!$template_path) {
                $template_path = get_stylesheet_directory() . '/includes/vc-elements/views/events-widget-view.php';
            }
            
            // Include the template file
            if (file_exists($template_path)) {
                ob_start();
                include $template_path;
                return ob_get_clean();
            }
            
            return '';
        }
    }

    new NAI_Events_Widget();
} 