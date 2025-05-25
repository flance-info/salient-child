<?php
/**
 * Theme Functions
 *
 * @package Salient Child Theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Properly enqueue styles and scripts.
require_once get_stylesheet_directory() . '/includes/enqueu.php';
require_once get_stylesheet_directory() . '/includes/nai-events-post-type.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-events-widget.php';
require_once get_stylesheet_directory() . '/includes/nai-opinions-post-type.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-opinions-widget.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-media-file-link-widget.php';
require_once get_stylesheet_directory() . '/includes/analytics-materials-post-type.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/analytics-materials-widget.php';
require_once get_stylesheet_directory() . '/includes/ajax-handlers.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-rss-widget.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-calendar-widget.php';
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-photo-gallery-widget.php';
require_once get_stylesheet_directory() . '/includes/nai-photo-gallery-post-type.php';

add_action('wp_footer', function () {
    ?>
    <div id="mm_inline_2" class="mfp-hide">
        <?php echo do_shortcode('[contact-form-7 id="bc0e06a" title="Контакт рус"]'); ?>  

    </div>
    <?php
});

add_filter('wpcf7_autop_or_not', '__return_false');

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('nai-photo-gallery-widget', get_stylesheet_directory_uri() . '/assets/css/nai-photo-gallery-widget.css');
});

add_action('after_setup_theme', function() {
    load_child_theme_textdomain('salient-child', get_stylesheet_directory() . '/languages');
});

add_filter('tiny_mce_before_init', 'disable_nbsp_in_editor');
function disable_nbsp_in_editor($init) {
    $init['entity_encoding'] = 'raw';
    $init['remove_linebreaks'] = false;
    $init['forced_root_block'] = false;
    $init['force_br_newlines'] = true;
    $init['force_p_newlines'] = false;
    return $init;
}

function salient_breadcrumb_shortcode() {
    if (function_exists('nectar_breadcrumbs')) {
        ob_start();
        nectar_breadcrumbs();
        return ob_get_clean();
    }
}
add_shortcode('salient_breadcrumbs', 'salient_breadcrumb_shortcode');
?>