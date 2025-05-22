<?php
if (!defined('ABSPATH')) exit;

class NAI_RSS_Widget {
    public function __construct() {
        add_action('vc_before_init', array($this, 'register_vc_element'));
        add_shortcode('nai_rss_widget', array($this, 'shortcode'));
    }

    public function register_vc_element() {
        vc_map(array(
            'name' => esc_html__('RSS Aggregator', 'salient-child'),
            'base' => 'nai_rss_widget',
            'category' => esc_html__('NAI Elements', 'salient-child'),
            'icon' => 'icon-wpb-application-icon-large',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('RSS Feed URL 1', 'salient-child'),
                    'param_name' => 'url1',
                    'value' => 'https://davaktiv.uz/rss',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('RSS Feed URL 2', 'salient-child'),
                    'param_name' => 'url2',
                    'value' => 'https://antimon.gov.uz/ru/feed/',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('RSS Feed URL 3', 'salient-child'),
                    'param_name' => 'url3',
                    'value' => 'https://cbu.uz/ru/press_center/news/rss',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Number of items to display', 'salient-child'),
                    'param_name' => 'count',
                    'value' => '3',
                ),
            ),
        ));
    }

    public function shortcode($atts) {
        $atts = shortcode_atts(array(
            'url1' => '',
            'url2' => '',
            'url3' => '',
            'count' => 3,
        ), $atts, 'nai_rss_widget');

        $feeds = array_filter([$atts['url1'], $atts['url2'], $atts['url3']]);
        $items = array();

        foreach ($feeds as $feed_url) {
            $rss = fetch_feed($feed_url);
            if (!is_wp_error($rss)) {
                foreach ($rss->get_items(0, 5) as $item) {
                    $items[] = array(
                        'title' => $item->get_title(),
                        'link' => $item->get_link(),
                        'date' => $item->get_date('U'),
                        'date_display' => $item->get_date('d.m.Y'),
                        'desc' => $item->get_description(),
                        'image' => $this->get_image_from_item($item),
                    );
                }
            }
        }

        // Sort by date desc
        usort($items, function($a, $b) {
            return $b['date'] - $a['date'];
        });
        $items = array_slice($items, 0, intval($atts['count']));

        ob_start();
        $view = locate_template('vc_elements/views/rss-widget-view.php');
        if ($view) {
            include $view;
        } else {
            echo '<p>' . esc_html__('RSS widget view not found.', 'salient-child') . '</p>';
        }
        return ob_get_clean();
    }

    private function get_image_from_item($item) {
        // Try to get image from enclosure or content
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_link()) {
            return esc_url($enclosure->get_link());
        }
        $content = $item->get_content();
        if (preg_match('/<img[^>]+src=["\\\']([^"\\\']+)["\\\']/i', $content, $matches)) {
            return esc_url($matches[1]);
        }
        $desc = $item->get_description();
        if (preg_match('/<img[^>]+src=["\\\']([^"\\\']+)["\\\']/i', $desc, $matches)) {
            return esc_url($matches[1]);
        }
        return get_stylesheet_directory_uri() . '/img/rss-placeholder.jpg'; // fallback
    }
}

new NAI_RSS_Widget(); 