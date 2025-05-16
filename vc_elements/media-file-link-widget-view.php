<?php
$style = '';
if (!empty($atts['bg_color'])) $style .= 'background:' . esc_attr($atts['bg_color']) . ';';
if (!empty($atts['radius'])) $style .= 'border-radius:' . esc_attr($atts['radius']) . 'px;';
if (!empty($atts['font_family'])) $style .= 'font-family:' . esc_attr($atts['font_family']) . ';';
if (!empty($atts['font_size'])) $style .= 'font-size:' . esc_attr($atts['font_size']) . 'px;';
if (!empty($atts['padding'])) $style .= 'padding:' . esc_attr($atts['padding']) . ';';
if (!empty($atts['margin'])) $style .= 'margin:' . esc_attr($atts['margin']) . ';';
$title_tag = in_array($atts['title_tag'], ['h1','h2','h3','h4','h5','h6','p','span']) ? $atts['title_tag'] : 'h3';
?>
<div class="nai-media-file-link-block" style="<?php echo esc_attr($style); ?>">
    <a href="<?php echo esc_url($file_url); ?>" class="nai-media-file-link" download>
        <span class="nai-media-file-link-icon" aria-hidden="true">
            <?php if (!empty($icon_img)) {
                echo $icon_img;
            } ?>
        </span>
        <span class="nai-media-file-link-content">
            <<?php echo $title_tag; ?> class="nai-media-file-link-title"><?php echo esc_html($file_title); ?></<?php echo $title_tag; ?>>
            <span class="nai-media-file-link-meta"><?php echo esc_html($file_ext); ?><?php if ($file_size) echo ' (' . esc_html($file_size) . ')'; ?></span>
        </span>
    </a>
</div> 