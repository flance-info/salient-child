<div class="nai-opinions-widget">
    <?php if ($opinions->have_posts()): ?>
        <?php while ($opinions->have_posts()): $opinions->the_post();
            $author_name = get_post_meta(get_the_ID(), '_nai_opinion_author_name', true);
            $author_position = get_post_meta(get_the_ID(), '_nai_opinion_author_position', true);
            $author_photo_id = get_post_meta(get_the_ID(), '_nai_opinion_author_photo_id', true);
            $author_photo = $author_photo_id ? wp_get_attachment_image($author_photo_id, 'thumbnail', false, ['class' => 'opinion-author-photo']) : '';
            $opinion_date = get_the_date('d.m.Y');
        ?>
        <div class="nai-opinion-row">
            <div class="nai-opinion-author">
                <?php if ($author_photo): ?>
                    <div class="nai-opinion-author-photo"><?php echo $author_photo; ?></div>
                <?php endif; ?>
                <div class="nai-opinion-author-name"><?php echo esc_html($author_name); ?></div>
                <div class="nai-opinion-author-position"><?php echo esc_html($author_position); ?></div>
            </div>
            <div class="nai-opinion-content">
                <div class="nai-opinion-date"><?php echo esc_html($opinion_date); ?></div>
                <div class="nai-opinion-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                <div class="nai-opinion-excerpt"><?php the_excerpt(); ?></div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php else: ?>
        <p><?php esc_html_e('Нет мнений для отображения.', 'salient-child'); ?></p>
    <?php endif; ?>
</div>
