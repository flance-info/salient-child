<?php
if (!defined('ABSPATH')) exit;

class NAI_RSS_Widget {
    public function __construct() {
        add_action('vc_before_init', array($this, 'register_vc_element'));
        add_shortcode('nai_rss_widget', array($this, 'shortcode'));
        add_filter('wp_feed_options', function($feed) {
            $feed->force_feed(true); // Force parsing even if WP thinks it's invalid
            return $feed;
        });
        
        add_filter('http_request_args', function($args, $url) {
            if (strpos($url, 'cbu.uz') !== false) {
                $args['timeout'] = 15;
                $args['headers']['User-Agent'] = 'Mozilla/5.0 (RSS Reader)';
            }
            return $args;
        }, 10, 2);

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

    public function shortcode($atts = array()) {
        $atts = shortcode_atts(array(
            'url1' => 'https://davaktiv.uz/rss',
            'url2' => 'https://raqobat.gov.uz/ru/feed/',
            'url3' => 'https://cbu.uz/ru/press_center/news/rss/',
            'count' => 3,
        ), (array) $atts, 'nai_rss_widget');

        $feeds = array_filter([$atts['url1'], $atts['url2'], $atts['url3']]);
        $items = array();

        foreach ($feeds as $feed_url) {
            add_filter('https_ssl_verify', '__return_false');
            $rss = fetch_feed($feed_url);
            remove_filter('https_ssl_verify', '__return_false');
            if (is_wp_error($rss)) {
                // Fallback: try manual fetch and parse
                $response = wp_remote_get($feed_url);
                if (!is_wp_error($response)) {
                    $body = wp_remote_retrieve_body($response);
                    $xml = simplexml_load_string($body);

                    
                    if ($xml && isset($xml->channel->item[0])) {
                        $item = $xml->channel->item[0];
                        $image = isset($item->enclosure['url']) ? (string)$item->enclosure['url'] : get_stylesheet_directory_uri() . '/assets/img/rssfeed.png';
                        
                        
                        
                        
                        $items[] = array(
                            'title' => (string)$item->title,
                            'link' => (string)$item->link,
                            'date' => strtotime((string)$item->pubDate),
                            'date_display' => date('d.m.Y', strtotime((string)$item->pubDate)),
                            'desc' => (string)$item->description,
                            'image' => $image,
                        );
                        continue;
                    }
                }
                $error_messages = $rss->get_error_messages();
                foreach ($error_messages as $error) {
                   // echo '<div style="color:red;">RSS Error for ' . esc_html($feed_url) . ': ' . esc_html($error) . '</div>';
                }
                continue;
            }
            if (!is_wp_error($rss)) {
                
                $item = $rss->get_item(0);
                
           
                if ($item) {
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

      

      
        ob_start();
        $view = locate_template('vc_elements/rss-widget-view.php');
        if ($view) {
            include $view;
        } else {
            echo '<p>' . esc_html__('RSS widget view not found.', 'salient-child') . '</p>';
        }
        return ob_get_clean();
    }

    private function get_image_from_item($item) {
        // Try to get image from enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_link()) {
            return esc_url($enclosure->get_link());
        }

        // Try to get image from media:content
        $media_content = $item->get_item_tags('http://search.yahoo.com/mrss/', 'content');
        if (!empty($media_content) && !empty($media_content[0]['attribs']['']['url'])) {
            return esc_url($media_content[0]['attribs']['']['url']);
        }

        // Try to get image from description or content
        $content = $item->get_content();
        if (preg_match('/<img[^>]+src=["\\\']([^"\\\']+)["\\\']/i', $content, $matches)) {
            return esc_url($matches[1]);
        }
        $desc = $item->get_description();
        if (preg_match('/<img[^>]+src=["\\\']([^"\\\']+)["\\\']/i', $desc, $matches)) {
            return esc_url($matches[1]);
        }

        // Fallback placeholder
        return get_stylesheet_directory_uri() . '/assets/img/rssfeed.png';
    }
}

new NAI_RSS_Widget();