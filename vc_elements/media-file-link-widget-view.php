<div class="nai-media-file-link-block">
    <a href="<?php echo esc_url($file_url); ?>" class="nai-media-file-link" download>
        <span class="nai-media-file-link-icon" aria-hidden="true">
            <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#7A8B7A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="4" y="17" width="16" height="4" rx="2" fill="#7A8B7A"/></svg>
        </span>
        <span class="nai-media-file-link-content">
            <span class="nai-media-file-link-title"><?php echo esc_html($file_title); ?></span>
            <span class="nai-media-file-link-meta"><?php echo esc_html($file_ext); ?><?php if ($file_size) echo ' (' . esc_html($file_size) . ')'; ?></span>
        </span>
    </a>
</div> 