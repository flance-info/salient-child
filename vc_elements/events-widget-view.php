<?php
/**
 * Events Widget View Template
 * @package NAI_Theme
 */

if (!defined('ABSPATH')) exit;

// Get all unique years from event dates
global $wpdb;
$years = $wpdb->get_col("
    SELECT DISTINCT YEAR(meta_value) 
    FROM {$wpdb->postmeta} 
    WHERE meta_key = '_nai_event_date' 
    ORDER BY meta_value DESC
");

$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
if (!in_array($current_year, $years)) {
    $current_year = date('Y');
}

$current_tab = isset($_GET['tab']) && $_GET['tab'] === 'past' ? 'past' : 'upcoming';
?>
<div class="nai-events-widget">
    <div class="nai-events-header">
        <h2>
            <?php echo esc_html__( 'Мероприятия', 'salient-child' ); ?>
            <div class="nai-events-year-select">
                <span class="nai-events-year"><?php echo esc_html($current_year); ?></span>
                <span class="nai-events-year-dropdown">&#9660;</span>
                <div class="nai-events-year-options">
                    <?php foreach ($years as $year) : ?>
                        <a href="#" data-year="<?php echo esc_attr($year); ?>" 
                           <?php echo $current_year == $year ? 'class="active"' : ''; ?>>
                            <?php echo esc_html($year); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </h2>
    </div>
    <div class="nai-events-tabs">
        <a href="#" class="nai-events-tab<?php echo $current_tab === 'upcoming' ? ' active' : ''; ?>" 
           data-tab="upcoming">Ожидаемые</a>
        <a href="#" class="nai-events-tab<?php echo $current_tab === 'past' ? ' active' : ''; ?>" 
           data-tab="past">Прошедшие</a>
    </div>
    <div class="nai-events-list">
        <?php
        $start_date = $current_year . '-01-01';
        $end_date = $current_year . '-12-31';
        
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

        if ($current_tab === 'past') {
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
            'order' => $current_tab === 'past' ? 'DESC' : 'ASC',
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
        ?>
    </div>
    <?php if ($events->found_posts > 10) : ?>
    <div class="nai-events-pagination">
        <a href="#" class="active">1</a>
        <a href="#">2</a>
        <a href="#">3</a>
        <span>...</span>
        <a href="#">5</a>
    </div>
    <?php endif; ?>
</div>

