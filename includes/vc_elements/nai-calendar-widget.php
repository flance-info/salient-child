<?php
if (!defined('ABSPATH')) exit;

class NAI_Calendar_Widget {
    public function __construct() {
        add_action('vc_before_init', array($this, 'register_vc_element'));
        add_shortcode('nai_calendar_widget', array($this, 'shortcode'));
    }

    public function register_vc_element() {
        vc_map(array(
            'name' => esc_html__('Calendar', 'salient-child'),
            'base' => 'nai_calendar_widget',
            'category' => esc_html__('NAI Elements', 'salient-child'),
            'icon' => 'icon-wpb-application-icon-large',
            'params' => array(),
        ));
    }

    public function shortcode($atts = array()) {
        ob_start();
        ?>
        <div class="calendar-container nai-calendar-widget">
            <div class="calendar-header">
                <div class="calendar-title"><?php echo esc_html__('Calendar', 'salient-child'); ?></div>
            </div>
            <div class="month-selector">
                <div class="month-nav" data-dir="prev">&#10094;</div>
                <div class="month-label"></div>
                <div class="month-nav" data-dir="next">&#10095;</div>
            </div>
            <div class="calendar-grid"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
new NAI_Calendar_Widget(); 