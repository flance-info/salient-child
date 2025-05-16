<?php
/**
 * NAI Media File Link Includer Widget for Visual Composer
 */
if (!defined('ABSPATH')) exit;

if (!class_exists('NAI_Media_File_Link_Widget')) {
    class NAI_Media_File_Link_Widget {
        public function __construct() {
            add_action('vc_before_init', [$this, 'integrate_with_vc']);
            add_shortcode('nai_media_file_link', [$this, 'render_media_file_link']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_js']);
            if (function_exists('vc_add_shortcode_param')) {
                vc_add_shortcode_param('nai_media_file', [$this, 'nai_media_file_param'], get_stylesheet_directory_uri() . '/assets/js/nai-media-file-param.js');
            }
        }

        public function integrate_with_vc() {
            vc_map([
                'name' => esc_html__('Media File Link Includer', 'salient-child'),
                'description' => esc_html__('Upload or select a file and display a download link with custom design.', 'salient-child'),
                'base' => 'nai_media_file_link',
                'category' => esc_html__('NAI Elements', 'salient-child'),
                'icon' => 'vc_icon-vc-media-grid',
                'params' => [
                    [
                        'type' => 'nai_media_file',
                        'heading' => esc_html__('Select File', 'salient-child'),
                        'param_name' => 'file_id',
                        'description' => esc_html__('Upload or select a PDF or PPTX file from the media library.', 'salient-child'),
                    ],
                    [
                        'type' => 'attach_image',
                        'heading' => esc_html__('Icon/Image', 'salient-child'),
                        'param_name' => 'icon_image_id',
                        'description' => esc_html__('Upload or select an image to use as the icon.', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Title', 'salient-child'),
                        'param_name' => 'title',
                        'description' => esc_html__('Enter the file title to display.', 'salient-child'),
                    ],
                    [
                        'type' => 'dropdown',
                        'heading' => esc_html__('Title Tag', 'salient-child'),
                        'param_name' => 'title_tag',
                        'value' => [
                            'h1' => 'h1',
                            'h2' => 'h2',
                            'h3' => 'h3',
                            'h4' => 'h4',
                            'h5' => 'h5',
                            'h6' => 'h6',
                            'p' => 'p',
                            'span' => 'span',
                        ],
                        'std' => 'h3',
                        'description' => esc_html__('Select the HTML tag for the title.', 'salient-child'),
                    ],
                    [
                        'type' => 'colorpicker',
                        'heading' => esc_html__('Background Color', 'salient-child'),
                        'param_name' => 'bg_color',
                        'description' => esc_html__('Set the background color.', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Border Radius (px)', 'salient-child'),
                        'param_name' => 'radius',
                        'description' => esc_html__('Set the border radius in px.', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Font Family', 'salient-child'),
                        'param_name' => 'font_family',
                        'description' => esc_html__('Set the font family.', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Font Size (px)', 'salient-child'),
                        'param_name' => 'font_size',
                        'description' => esc_html__('Set the font size in px.', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Padding (CSS)', 'salient-child'),
                        'param_name' => 'padding',
                        'description' => esc_html__('Set the padding (e.g. 20px 30px).', 'salient-child'),
                    ],
                    [
                        'type' => 'textfield',
                        'heading' => esc_html__('Margin (CSS)', 'salient-child'),
                        'param_name' => 'margin',
                        'description' => esc_html__('Set the margin (e.g. 10px 0 10px 0).', 'salient-child'),
                    ],
                ],
            ]);
        }

        public function nai_media_file_param($settings, $value) {
            $output = '<div class="nai-media-file-field">';
            $output .= '<input type="hidden" name="' . esc_attr($settings['param_name']) . '" class="wpb_vc_param_value ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field" value="' . esc_attr($value) . '"/>';
            $file_url = $value ? wp_get_attachment_url($value) : '';
            $file_name = $file_url ? basename($file_url) : '';
            $output .= '<div class="nai-media-file-preview">' . ($file_name ? esc_html($file_name) : '') . '</div>';
            $output .= '<button class="button nai-media-file-upload">' . esc_html__('Select or Upload File', 'salient-child') . '</button>';
            $output .= '<button class="button nai-media-file-remove" style="display:' . ($value ? 'inline-block' : 'none') . ';">' . esc_html__('Remove', 'salient-child') . '</button>';
            $output .= '</div>';
            return $output;
        }

        public function enqueue_admin_js() {
            wp_enqueue_media();
        }

        public function render_media_file_link($atts) {
            $atts = shortcode_atts([
                'file_id' => '',
                'icon_image_id' => '',
                'title' => '',
                'title_tag' => 'h3',
                'bg_color' => '',
                'radius' => '',
                'font_family' => '',
                'font_size' => '',
                'padding' => '',
                'margin' => '',
            ], $atts);

            if (empty($atts['file_id'])) return '';
            $file_url = wp_get_attachment_url($atts['file_id']);
            $file_title = $atts['title'] ? $atts['title'] : get_the_title($atts['file_id']);
            $file_info = pathinfo($file_url);
            $file_ext = isset($file_info['extension']) ? '.' . $file_info['extension'] : '';
            $file_size = '';
            $file_path = get_attached_file($atts['file_id']);
            if ($file_path && file_exists($file_path)) {
                $file_size = size_format(filesize($file_path));
            }
            $icon_img = $atts['icon_image_id'] ? wp_get_attachment_image($atts['icon_image_id'], 'thumbnail', false, ['class' => 'nai-media-file-link-img']) : '';
            ob_start();
            $template_path = locate_template('vc_elements/media-file-link-widget-view.php');
            if (!$template_path) {
                $template_path = get_stylesheet_directory() . '/vc_elements/media-file-link-widget-view.php';
            }
            if (file_exists($template_path)) {
                include $template_path;
            }
            return ob_get_clean();
        }
    }
    new NAI_Media_File_Link_Widget();
} 