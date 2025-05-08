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
                $args['meta_key'] = '_event_date';
                $args['orderby'] = 'meta_value';
                $args['meta_type'] = 'DATE';
            } else {
                $args['orderby'] = $atts['orderby'];
            }

            $events = new WP_Query($args);
            $output = '';

            if ($events->have_posts()) {
                $output .= '<div class="nai-events-grid">';
                
                while ($events->have_posts()) {
                    $events->the_post();
                    $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                    $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                    $event_end_time = get_post_meta(get_the_ID(), '_event_end_time', true);
                    $event_location = get_post_meta(get_the_ID(), '_event_location', true);
                    $event_address = get_post_meta(get_the_ID(), '_event_address', true);
                    $event_city = get_post_meta(get_the_ID(), '_event_city', true);
                    $event_country = get_post_meta(get_the_ID(), '_event_country', true);
                    $event_contact = get_post_meta(get_the_ID(), '_event_contact', true);

                    $output .= '<div class="nai-event-item">';
                    $output .= '<div class="event-date">' . esc_html($event_date) . '</div>';
                    $output .= '<h3 class="event-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                    
                    if ($event_time) {
                        $output .= '<div class="event-time">';
                        $output .= esc_html($event_time);
                        if ($event_end_time) {
                            $output .= ' - ' . esc_html($event_end_time);
                        }
                        $output .= '</div>';
                    }

                    if ($event_location || $event_address || $event_city || $event_country) {
                        $output .= '<div class="event-location">';
                        if ($event_location) {
                            $output .= '<span class="location-name">' . esc_html($event_location) . '</span>';
                        }
                        if ($event_address || $event_city || $event_country) {
                            $output .= '<span class="address">';
                            if ($event_address) $output .= esc_html($event_address);
                            if ($event_city) $output .= ', ' . esc_html($event_city);
                            if ($event_country) $output .= ', ' . esc_html($event_country);
                            $output .= '</span>';
                        }
                        $output .= '</div>';
                    }

                    if ($event_contact) {
                        $output .= '<div class="event-contact">' . esc_html($event_contact) . '</div>';
                    }

                    $output .= '</div>'; // .nai-event-item
                }

                $output .= '</div>'; // .nai-events-grid
                wp_reset_postdata();
            }

            return $output;
        }
    }

    new NAI_Events_Widget();
} 