<div class="nai-media-file-link-block">
    <a href="<?php echo esc_url($file_url); ?>" class="nai-media-file-link" download>
        <span class="nai-media-file-link-icon" aria-hidden="true">
            <svg width="55" height="65" viewBox="0 0 55 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M27.5 10V41.25M27.5 41.25L16.0417 29.7917M27.5 41.25L38.9583 29.7917" stroke="#627A66" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="9.16663" y="51.0417" width="36.6667" height="8.125" rx="4.0625" fill="#627A66"/>
            </svg>
        </span>
        <span class="nai-media-file-link-content">
            <span class="nai-media-file-link-title"><?php echo esc_html($file_title); ?></span>
            <span class="nai-media-file-link-meta"><?php echo esc_html($file_ext); ?><?php if ($file_size) echo ' (' . esc_html($file_size) . ')'; ?></span>
        </span>
    </a>
</div> 