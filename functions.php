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



add_action('wp_footer', function () {
    ?>
    <div id="mm_inline_2" class="mfp-hide">
        <?php echo do_shortcode('[contact-form-7 id="bc0e06a" title="Контакт рус"]'); ?>  

    </div>
    <?php
});

add_filter('wpcf7_autop_or_not', '__return_false');






?>