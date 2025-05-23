<?php
if (!defined('ABSPATH')) exit;

class NAI_Ajax_Handlers {
    public function __construct() {
        add_action('wp_ajax_load_events', array($this, 'load_events'));
        add_action('wp_ajax_nopriv_load_events', array($this, 'load_events'));
        add_action('wp_ajax_nai_gallery_pagination', array($this, 'nai_gallery_pagination_ajax'));
        add_action('wp_ajax_nopriv_nai_gallery_pagination', array($this, 'nai_gallery_pagination_ajax'));
        add_action('wp_ajax_nai_gallery_archive', array($this, 'nai_gallery_archive_ajax'));
        add_action('wp_ajax_nopriv_nai_gallery_archive', array($this, 'nai_gallery_archive_ajax'));
    }

    public function load_events() {
        check_ajax_referer('nai_events_nonce', 'nonce');

        $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'upcoming';

        // Get events HTML
        ob_start();
        $this->render_events($year, $tab);
        $html = ob_get_clean();

        // Get pagination HTML
        ob_start();
        $this->render_pagination($year, $tab);
        $pagination = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'pagination' => $pagination
        ));
    }

    private function render_events($year, $tab) {
        $start_date = $year . '-01-01';
        $end_date = $year . '-12-31';
        
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => '_nai_event_date',
                'compare' => '>=',
                'value' => $start_date,
                'type' => 'DATE'
            ),
            array(
                'key' => '_nai_event_date',
                'compare' => '<=',
                'value' => $end_date,
                'type' => 'DATE'
            )
        );

        if ($tab === 'past') {
            $meta_query[] = array(
                'key' => '_nai_event_date',
                'compare' => '<',
                'value' => date('Y-m-d'),
                'type' => 'DATE'
            );
        } else {
            $meta_query[] = array(
                'key' => '_nai_event_date',
                'compare' => '>=',
                'value' => date('Y-m-d'),
                'type' => 'DATE'
            );
        }

        $args = array(
            'post_type' => 'nai_event',
            'posts_per_page' => 10,
            'meta_key' => '_nai_event_date',
            'orderby' => 'meta_value',
            'order' => $tab === 'past' ? 'DESC' : 'ASC',
            'meta_query' => array($meta_query),
            'post_status' => 'publish'
        );

        $events = new WP_Query($args);

        if ($events->have_posts()) :
            while ($events->have_posts()) : $events->the_post();
                $event_date = get_post_meta(get_the_ID(), '_nai_event_date', true);
                $event_start_time = get_post_meta(get_the_ID(), '_nai_event_start_time', true);
                $event_end_time = get_post_meta(get_the_ID(), '_nai_event_end_time', true);
                $event_city = get_post_meta(get_the_ID(), '_nai_event_city', true);
                ?>
                <div class="nai-event-row">
                    <div class="nai-event-col-left">
                        <div class="nai-event-date"><?php echo esc_html(date_i18n('d F Y', strtotime($event_date))); ?></div>
                        <div class="nai-event-time">
                            <?php echo esc_html($event_start_time); ?>
                            <?php if ($event_end_time) echo '–' . esc_html($event_end_time); ?>
                        </div>
                    </div>
                    <div class="nai-event-col-main">
                        <?php if ($event_city): ?>
                            <span class="nai-event-city">
                                <svg class="nai-event-city-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="7" r="2" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M8 14C8 14 2.5 9.5 2.5 6.5C2.5 3.73858 5.23858 1.5 8 1.5C10.7614 1.5 13.5 3.73858 13.5 6.5C13.5 9.5 8 14 8 14Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <?php echo esc_html($event_city); ?>
                            </span>
                        <?php endif; ?>
                        <div class="nai-event-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </div>
                        <div class="nai-event-arrow">→</div>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else:
            echo '<div class="nai-no-events">' . esc_html__('No events found', 'salient-child') . '</div>';
        endif;
    }

    private function render_pagination($year, $tab) {
        // Add your pagination logic here
        // This is just a placeholder
        echo '<div class="nai-events-pagination">';
        echo '<a href="#" class="active">1</a>';
        echo '<a href="#">2</a>';
        echo '<a href="#">3</a>';
        echo '<span>...</span>';
        echo '<a href="#">5</a>';
        echo '</div>';
    }

    
function nai_gallery_pagination_ajax() {
    $post_id = intval($_POST['post_id']);
    $paged = intval($_POST['paged']);
    $images_per_page = 12;

    $images = get_post_meta($post_id, '_pg_images', true);
    $img_count = is_array($images) ? count($images) : 0;
    $total_pages = $img_count > 0 ? ceil($img_count / $images_per_page) : 1;
    $offset = ($paged - 1) * $images_per_page;
    $images_to_show = $img_count > 0 ? array_slice($images, $offset, $images_per_page) : [];

    ob_start();
    ?>
    <?php if ($images_to_show): foreach ($images_to_show as $img_id): 
        $img_url = wp_get_attachment_image_url($img_id, 'large');
        $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
        ?>
        <a href="<?php echo esc_url($img_url); ?>" class="pg-gallery-img" data-fancybox="gallery">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="">
        </a>
    <?php endforeach; endif; ?>
    <?php
    $gallery_html = ob_get_clean();

    // Pagination
    $pagination = paginate_links([
        'total' => $total_pages,
        'current' => $paged,
        'format' => '#',
        'prev_text' => '&lt;',
        'next_text' => '&gt;',
        'type' => 'array'
    ]);
    ob_start();
    if ($pagination) {
        echo '<div class="pg-single-pagination" style="text-align:center;margin:32px 0 0 0;">';
        foreach ($pagination as $link) {
            echo str_replace('href="#"', '', $link); // Remove href, we'll handle click in JS
        }
        echo '</div>';
    }
    $pagination_html = ob_get_clean();

    wp_send_json([
        'gallery' => $gallery_html,
        'pagination' => $pagination_html
    ]);
} 

public function nai_gallery_archive_ajax() {
    $year = isset($_POST['year']) ? intval($_POST['year']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $per_page = 6;
    $columns = 3;

    // Get all years for selector
    $years = get_posts([
        'post_type' => 'photo_gallery',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ]);
    $year_options = [];
    foreach ($years as $pid) {
        $y = get_post_meta($pid, '_pg_year', true);
        if ($y) $year_options[$y] = $y;
    }
    krsort($year_options);

    $args = [
        'post_type' => 'photo_gallery',
        'posts_per_page' => $per_page,
        'orderby' => 'date',
        'order' => 'DESC',
        'paged' => $paged,
    ];
    if (!empty($year)) {
        $args['meta_query'] = [[
            'key' => '_pg_year',
            'value' => $year,
            'compare' => '=',
        ]];
    }
    $q = new WP_Query($args);
    $total_pages = $q->max_num_pages;
    ob_start();
    ?>
    <div class="nai-pg-gallery-archive">
        <div class="nai-pg-gallery-header">
            <h2 class="nai-pg-gallery-title">Фотогалереи <span class="nai-pg-gallery-year-select-wrap">
                <select class="nai-pg-gallery-year-select">
                    <option value="">Все</option>
                    <?php foreach($year_options as $y): ?>
                        <option value="<?php echo esc_attr($y); ?>"<?php if($year==$y)echo ' selected';?>><?php echo esc_html($y); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            </h2>
        </div>
        <div class="nai-pg-vc-grid nai-pg-gallery-list" style="display:grid;grid-template-columns:repeat(<?php echo intval($columns); ?>,1fr);gap:32px;">
            <?php
            if ($q->have_posts()):
                while ($q->have_posts()): $q->the_post();
                    $year_val = get_post_meta(get_the_ID(), '_pg_year', true);
                    $images = get_post_meta(get_the_ID(), '_pg_images', true);
                    $img_count = is_array($images) ? count($images) : 0;
                    $cover = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$cover && $img_count) $cover = wp_get_attachment_image_url($images[0], 'large');
                    $date = get_the_date('d.m.Y');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="pg-archive-card">
                        <div class="pg-archive-card-img" style="background-image:url('<?php echo esc_url($cover); ?>');height:270px;">
                            <div class="pg-archive-card-overlay"></div>
                            <div class="pg-archive-card-info">
                                <div class="pg-archive-card-meta">
                                    <span class="pg-archive-card-date"><svg width="18" height="18" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?php echo esc_html($year_val); ?></span>
                                    <span class="pg-archive-card-count"><svg width="18" height="18" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10.5" r="1.5"/><path d="M21 19l-5.5-7-4.5 6-3-4-4 5"/></svg> <?php echo esc_html($img_count); ?> фото</span>
                                </div>
                                <div class="pg-archive-card-title"><?php the_title(); ?></div>
                            </div>
                        </div>
                    </a>
                <?php endwhile;
            else: ?>
                <p>Галереи не найдены.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>
        <?php if($total_pages>1): ?>
        <div class="nai-pg-gallery-pagination">
            <?php echo paginate_links([
                'total' => $total_pages,
                'current' => $paged,
                'format' => '#',
                'prev_text' => '&lt;',
                'next_text' => '&gt;',
            ]); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json(['html' => $html]);
}
}

new NAI_Ajax_Handlers();
