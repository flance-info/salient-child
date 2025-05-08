<?php
/**
 * Events Widget View Template
 * @package NAI_Theme
 */

if (!defined('ABSPATH')) exit;

$current_year = date('Y');
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';
?>
<div class="nai-events-widget">
    <div class="nai-events-header">
        <h2>
            Мероприятия
            <span class="nai-events-year"><?php echo esc_html($current_year); ?></span>
            <span class="nai-events-year-dropdown">&#9660;</span>
        </h2>
    </div>
    <div class="nai-events-tabs">
        <a href="#" class="nai-events-tab<?php if ($current_tab === 'upcoming') echo ' active'; ?>">Ожидаемые</a>
        <a href="#" class="nai-events-tab<?php if ($current_tab === 'past') echo ' active'; ?>">Прошедшие</a>
    </div>
    <div class="nai-events-list">
        <?php
        $i = 0;
        if ($events->have_posts()) :
            while ($events->have_posts()) : $events->the_post();
                $event_date = get_post_meta(get_the_ID(), '_nai_event_date', true);
                $event_start_time = get_post_meta(get_the_ID(), '_nai_event_start_time', true);
                $event_end_time = get_post_meta(get_the_ID(), '_nai_event_end_time', true);
                $event_city = get_post_meta(get_the_ID(), '_nai_event_city', true);
                $is_active = ($i === 1); // Example: highlight the 2nd event as active
        ?>
        <div class="nai-event-row<?php if ($is_active) echo ' active'; ?>">
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
                        <svg class="nai-event-city-icon" width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="7" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 14C8 14 2.5 9.5 2.5 6.5C2.5 3.73858 5.23858 1.5 8 1.5C10.7614 1.5 13.5 3.73858 13.5 6.5C13.5 9.5 8 14 8 14Z" stroke="currentColor" stroke-width="1.5"/></svg>
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
            $i++;
            endwhile;
        endif;
        ?>
    </div>
    <div class="nai-events-pagination">
        <a href="#" class="active">1</a>
        <a href="#">2</a>
        <a href="#">3</a>
        <span>...</span>
        <a href="#">5</a>
    </div>
</div>
<?php wp_reset_postdata(); ?>