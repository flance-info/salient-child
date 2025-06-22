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
require_once get_stylesheet_directory() . '/includes/vc_elements/nai-currency-widget.php';
require_once get_stylesheet_directory() . '/includes/mobile-header.php';
require_once get_stylesheet_directory() . '/includes/helpers.php';

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
    wp_enqueue_style('salient-child-mobile', get_stylesheet_directory_uri() . '/assets/css/mobile.css', array(), '1.0.0', 'screen and (max-width: 767px)');
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

// Remove the original breadcrumbs action
function remove_parent_breadcrumbs() {
    remove_action('nectar_hook_before_content', 'nectar_yoast_breadcrumbs');
}
add_action('init', 'remove_parent_breadcrumbs');

// Add your custom breadcrumbs action
function custom_yoast_breadcrumbs() {
    global $post;

    // Check if we're on a single post of specific types
    if (function_exists('yoast_breadcrumb') && $post && 
        ($post->post_type === 'nai_event' || 
         $post->post_type === 'nai_opinion' ||
         $post->post_type === 'post')) {
        yoast_breadcrumb('<p id="breadcrumbs" class="yoast">', '</p>');
    }
}
add_action('nectar_hook_before_content', 'custom_yoast_breadcrumbs');

add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('mobile_logo_section', array(
        'title'    => __('Mobile Logo', 'salient-child'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('mobile_logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mobile_logo', array(
        'label'    => __('Mobile Logo', 'salient-child'),
        'section'  => 'mobile_logo_section',
        'settings' => 'mobile_logo',
    )));

    // Add second mobile logo
    $wp_customize->add_setting('mobile_logo_menu', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mobile_logo_menu', array(
        'label'    => __('Mobile Menu Logo', 'salient-child'),
        'section'  => 'mobile_logo_section', // Use the same section for grouping
        'settings' => 'mobile_logo_menu',
    )));
});
?>