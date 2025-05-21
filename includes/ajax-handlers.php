<?php
if (!defined('ABSPATH')) exit;

class NAI_Ajax_Handlers {
    public function __construct() {
        add_action('wp_ajax_load_events', array($this, 'load_events'));
        add_action('wp_ajax_nopriv_load_events', array($this, 'load_events'));
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
}

new NAI_Ajax_Handlers(); 