<?php

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles', 100);

function salient_child_enqueue_styles() {
		
		$nectar_theme_version = nectar_get_theme_version();
		wp_enqueue_style( 'salient-child-style', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version );
		wp_enqueue_script( 'salient-child-helper-script', get_stylesheet_directory_uri() . '/assets/js/helpers.js', array('jquery'), time(), true );
		wp_enqueue_style( 'nai-events-widget-style', get_stylesheet_directory_uri() . '/assets/css/nai-events-widget.css', '', $nectar_theme_version );
    if ( is_rtl() ) {
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
		}
}