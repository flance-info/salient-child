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

// Add additional includes below as needed.
// require_once get_stylesheet_directory() . '/includes/your-custom-file.php';

?>