<?php
get_header();

$year = get_post_meta(get_the_ID(), '_pg_year', true);
$images = get_post_meta(get_the_ID(), '_pg_images', true);
$img_count = is_array($images) ? count($images) : 0;
$date = get_the_date('d.m.Y');

// Pagination for images
$images_per_page = 12;
$paged = max(1, intval(get_query_var('paged', 1)));
$total_pages = $img_count > 0 ? ceil($img_count / $images_per_page) : 1;
$offset = ($paged - 1) * $images_per_page;
$images_to_show = $img_count > 0 ? array_slice($images, $offset, $images_per_page) : [];

// Enqueue Fancybox for lightbox
wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js', [], null, true);
add_action('wp_footer', function() {
    echo "<script>if(window.Fancybox){Fancybox.bind('[data-fancybox=gallery]');}</script>";
});
?>

<div class="pg-single-container" style="max-width:1280px;margin:40px auto 0 auto;padding:0 20px;">
    <div class="pg-single-breadcrumbs" style="color:#888;font-size:0.95rem;margin-bottom:18px;">
        <?php if(function_exists('yoast_breadcrumb')) yoast_breadcrumb('<p id="breadcrumbs">','</p>'); ?>
    </div>
    <h1 class="pg-single-title" style="font-size:2.4rem;font-weight:600;margin-bottom:12px;line-height:1.1;"><?php the_title(); ?></h1>
    <div class="pg-single-desc" style="margin-bottom:18px;max-width:800px;">
        <?php the_content(); ?>
    </div>
    <div class="pg-single-meta" style="display:flex;align-items:center;gap:24px;color:#888;font-size:1.05rem;margin-bottom:32px;">
        <span><svg width="24" height="24" style="vertical-align:middle;margin-right:4px;" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?php echo esc_html($date); ?></span>
        <span>
            <svg width="24" height="24" style="vertical-align:middle;margin-right:4px;" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2" stroke="#888" stroke-width="1.5" fill="none"/>
                <circle cx="8.5" cy="10.5" r="1.5" stroke="#888" stroke-width="1.5" fill="none"/>
                <path d="M21 19l-5.5-7-4.5 6-3-4-4 5" stroke="#888" stroke-width="1.5" fill="none"/>
            </svg>
            <?php echo esc_html($img_count); ?>
        </span>
    </div>
    <div class="pg-single-gallery-wrap" data-post-id="<?php echo get_the_ID(); ?>">
        <div class="pg-single-gallery">
            <?php if ($images_to_show): foreach ($images_to_show as $img_id): 
                $img_url = wp_get_attachment_image_url($img_id, 'large');
                $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                ?>
                <a href="<?php echo esc_url($img_url); ?>" class="pg-gallery-img" data-fancybox="gallery">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="">
                </a>
            <?php endforeach; endif; ?>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pg-single-pagination" style="text-align:center;margin:32px 0 0 0;">
            <?php
            echo paginate_links([
                'total' => $total_pages,
                'current' => $paged,
                'format' => '#',
                'prev_text' => '&lt;',
                'next_text' => '&gt;',
            ]);
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?> 