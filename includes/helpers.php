
// Custom WPML Language Switcher Shortcode
function custom_wpml_language_switcher_shortcode($atts) {
    // Check if WPML is active
    if (!function_exists('icl_get_languages')) {
        return '';
    }
    
    $atts = shortcode_atts(array(
        'show_flags' => 'no',
        'show_names' => 'no'
    ), $atts);
    
    $languages = icl_get_languages('skip_missing=0&orderby=code');
    
    if (empty($languages)) {
        return '';
    }
    
    $current_lang = ICL_LANGUAGE_CODE;
    
    ob_start();
    ?>
    <div class="wpml-language-switcher">
        <select onchange="window.location.href=this.value" style="padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; background: white; cursor: pointer;">
            <?php foreach ($languages as $lang): ?>
                <option value="<?php echo esc_url($lang['url']); ?>" <?php echo ($lang['language_code'] == $current_lang) ? 'selected' : ''; ?>>
                    <?php 
                    if ($atts['show_flags'] == 'yes') {
                        echo '<img src="' . $lang['country_flag_url'] . '" alt="' . $lang['language_code'] . '" style="width: 16px; margin-right: 5px;"> ';
                    }
                    echo strtoupper($lang['language_code']);
                    if ($atts['show_names'] == 'yes') {
                        echo ' - ' . $lang['native_name'];
                    }
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('wpml_dropdown', 'custom_wpml_language_switcher_shortcode');

// Visual Composer Integration for WPML Language Switcher
add_action('init', 'wpml_language_switcher_vc_map');
function wpml_language_switcher_vc_map() {
    if (function_exists('vc_map')) {
        vc_map(array(
            "name" => "WPML Language Switcher",
            "base" => "wpml_dropdown",
            "category" => "Content",
            "description" => "Add WPML language switcher dropdown",
            "icon" => "icon-wpb-layer-shape-text",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "heading" => "Show Flags",
                    "param_name" => "show_flags",
                    "value" => array(
                        "No" => "no",
                        "Yes" => "yes"
                    ),
                    "std" => "no"
                ),
                array(
                    "type" => "dropdown",
                    "heading" => "Show Language Names",
                    "param_name" => "show_names",
                    "value" => array(
                        "No" => "no",
                        "Yes" => "yes"
                    ),
                    "std" => "no"
                )
            )
        ));
    }
}

