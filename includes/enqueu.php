<?php

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles', 100);

function salient_child_enqueue_styles() {
		
		$nectar_theme_version = nectar_get_theme_version();
		wp_enqueue_style( 'salient-child-style', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version );
		wp_enqueue_script( 'salient-child-helper-script', get_stylesheet_directory_uri() . '/assets/js/helpers.js', array('jquery'), time(), true );
		wp_enqueue_style( 'nai-events-widget-style', get_stylesheet_directory_uri() . '/assets/css/nai-events-widget.css', '', $nectar_theme_version );
		wp_enqueue_style('nai-opinions-widget-style', get_stylesheet_directory_uri() . '/assets/css/nai-opinions-widget.css', '', $nectar_theme_version);
        wp_enqueue_style('nai-media-file-link-widget-style', get_stylesheet_directory_uri() . '/assets/css/nai-media-file-link-widget.css', '', $nectar_theme_version);
        wp_enqueue_style('analytics-materials-widget-style', get_stylesheet_directory_uri() . '/assets/css/analytics-materials-widget.css', '', $nectar_theme_version);
        wp_enqueue_script(
            'magnific-popup',
            'https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/jquery.magnific-popup.min.js',
            array('jquery'),
            null,
            true
        );
        wp_enqueue_style(
            'magnific-popup-style',
            'https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/magnific-popup.css'
        );
        wp_enqueue_script(
            'modal-madness-custom',
            get_stylesheet_directory_uri() . '/assets/js/modal-madness-custom.js',
            array('jquery', 'magnific-popup'),
            time(),
            true
        );
    if ( is_rtl() ) {
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
		}

// Enqueue scripts
wp_enqueue_script('nai-events-widget', get_stylesheet_directory_uri() . '/js/events-widget.js', array('jquery'), '1.0', true);
wp_localize_script('nai-events-widget', 'nai_events', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('nai_events_nonce')
));

wp_enqueue_script('nai-calendar-widget', get_stylesheet_directory_uri() . '/js/nai-calendar-widget.js', array('jquery'), null, true);

// Localize months and days
wp_localize_script('nai-calendar-widget', 'naiCalendarI18n', array(
    'months' => array(
        __('January', 'salient-child'),
        __('February', 'salient-child'),
        __('March', 'salient-child'),
        __('April', 'salient-child'),
        __('May', 'salient-child'),
        __('June', 'salient-child'),
        __('July', 'salient-child'),
        __('August', 'salient-child'),
        __('September', 'salient-child'),
        __('October', 'salient-child'),
        __('November', 'salient-child'),
        __('December', 'salient-child'),
    ),
    'days' => array(
        __('Mon', 'salient-child'),
        __('Tue', 'salient-child'),
        __('Wed', 'salient-child'),
        __('Thu', 'salient-child'),
        __('Fri', 'salient-child'),
        __('Sat', 'salient-child'),
        __('Sun', 'salient-child'),
    ),
));

}

add_action('wp_enqueue_scripts', function() {
    if (is_singular('photo_gallery')) {
        wp_enqueue_script('nai-photo-gallery-ajax', get_stylesheet_directory_uri() . '/assets/js/nai-photo-gallery-ajax.js', [], null, true);
        wp_localize_script('nai-photo-gallery-ajax', 'ajaxurl', admin_url('admin-ajax.php'));
    }
    // Enqueue for gallery archive (where [nai_photo_gallery] is used)
    if (is_page() || is_home() || is_front_page() || is_archive()) {
        global $post;
        if (isset($post->post_content) && strpos($post->post_content, '[nai_photo_gallery') !== false) {
            wp_enqueue_script('nai-photo-gallery-archive', get_stylesheet_directory_uri() . '/assets/js/nai-photo-gallery-archive.js', [], null, true);
            wp_localize_script('nai-photo-gallery-archive', 'ajaxurl', admin_url('admin-ajax.php'));
        }
    }
});