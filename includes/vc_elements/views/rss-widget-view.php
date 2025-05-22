<?php
if (!defined('ABSPATH')) exit;
if (empty($items)) {
    echo '<div class="nai-rss-widget-empty">' . esc_html__('No news found.', 'salient-child') . '</div>';
    return;
}
?>
<div class="nai-rss-widget">
    <?php foreach ($items as $item): ?>
        <div class="nai-rss-row">
            <div class="nai-rss-img">
                <a href="<?php echo esc_url($item['link']); ?>" target="_blank" rel="noopener">
                    <img src="<?php echo esc_url($item['image']); ?>" alt="" loading="lazy" />
                </a>
            </div>
            <div class="nai-rss-content">
                <div class="nai-rss-title">
                    <a href="<?php echo esc_url($item['link']); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html($item['title']); ?>
                    </a>
                </div>
                <div class="nai-rss-date">
                    <?php echo esc_html($item['date_display']); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> 