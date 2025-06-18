<?php


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
    $current_language = null;
    
    // Find current language details
    foreach ($languages as $lang) {
        if ($lang['language_code'] == $current_lang) {
            $current_language = $lang;
            break;
        }
    }
    
    ob_start();
    ?>
    <div class="wpml-language-switcher custom-dropdown">
        <div class="current-language" onclick="toggleLanguageDropdown(this)">
            <?php if ($atts['show_flags'] == 'yes' && $current_language): ?>
                <img src="<?php echo esc_url($current_language['country_flag_url']); ?>" 
                     alt="<?php echo esc_attr($current_language['language_code']); ?>" 
                     style="width: 16px; height: 12px; margin-right: 8px; object-fit: cover;">
            <?php endif; ?>
            <span class="language-code"><?php echo strtoupper($current_lang); ?></span>
            <?php if ($atts['show_names'] == 'yes' && $current_language): ?>
                <span class="language-name"> - <?php echo esc_html($current_language['native_name']); ?></span>
            <?php endif; ?>
            <span class="dropdown-arrow">▼</span>
        </div>
        <div class="language-options">
            <?php foreach ($languages as $lang): ?>
                <?php if ($lang['language_code'] != $current_lang): ?>
                    <a href="<?php echo esc_url($lang['url']); ?>" class="language-option">
                        <?php if ($atts['show_flags'] == 'yes'): ?>
                            <img src="<?php echo esc_url($lang['country_flag_url']); ?>" 
                                 alt="<?php echo esc_attr($lang['language_code']); ?>" 
                                 style="width: 16px; height: 12px; margin-right: 8px; object-fit: cover;">
                        <?php endif; ?>
                        <span class="language-code"><?php echo strtoupper($lang['language_code']); ?></span>
                        <?php if ($atts['show_names'] == 'yes'): ?>
                            <span class="language-name"> - <?php echo esc_html($lang['native_name']); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .wpml-language-switcher.custom-dropdown {
        position: relative;
        display: inline-block;
        font-family: inherit;
    }
    
    .wpml-language-switcher .current-language {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
        min-width: 80px;
    }
    
    .wpml-language-switcher .current-language:hover {
        border-color: #999;
        background: #f9f9f9;
    }
    
    .wpml-language-switcher .dropdown-arrow {
        margin-left: auto;
        font-size: 10px;
        transition: transform 0.2s ease;
    }
    
    .wpml-language-switcher.open .dropdown-arrow {
        transform: rotate(180deg);
    }
    
    .wpml-language-switcher .language-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        border-top: none;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
    }
    
    .wpml-language-switcher.open .language-options {
        display: block;
    }
    
    .wpml-language-switcher .language-option {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s ease;
        border-bottom: 1px solid #eee;
    }
    
    .wpml-language-switcher .language-option:last-child {
        border-bottom: none;
    }
    
    .wpml-language-switcher .language-option:hover {
        background: #f5f5f5;
    }
    
    .wpml-language-switcher .language-code {
        font-weight: 500;
    }
    
    .wpml-language-switcher .language-name {
        color: #666;
        font-size: 0.9em;
    }
    </style>
    
    <script>
    function toggleLanguageDropdown(element) {
        var dropdown = element.parentNode;
        var isOpen = dropdown.classList.contains('open');
        
        // Close all other dropdowns
        document.querySelectorAll('.wpml-language-switcher.open').forEach(function(el) {
            el.classList.remove('open');
        });
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.classList.add('open');
        }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.wpml-language-switcher')) {
            document.querySelectorAll('.wpml-language-switcher.open').forEach(function(el) {
                el.classList.remove('open');
            });
        }
    });
    </script>
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

