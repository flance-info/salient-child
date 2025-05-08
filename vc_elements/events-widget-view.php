<?php
/**
 * Template Name: Events Widget View
 * Template Part: Events Widget
 *
 * @package NAI_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template for displaying events in a grid layout
 *
 * @var WP_Query $events The events query object
 */

if ($events->have_posts()) : ?>
    <div class="nai-events-grid">
        <?php while ($events->have_posts()) : $events->the_post();
            $event_date = get_post_meta(get_the_ID(), '_nai_event_date', true);
            $event_start_time = get_post_meta(get_the_ID(), '_nai_event_start_time', true);
            $event_end_time = get_post_meta(get_the_ID(), '_nai_event_end_time', true);
            $event_location = get_post_meta(get_the_ID(), '_nai_event_location', true);
            $event_address = get_post_meta(get_the_ID(), '_nai_event_address', true);
            $event_city = get_post_meta(get_the_ID(), '_nai_event_city', true);
            $event_country = get_post_meta(get_the_ID(), '_nai_event_country', true);
            $event_contact_name = get_post_meta(get_the_ID(), '_nai_event_contact_name', true);
            $event_contact_phone = get_post_meta(get_the_ID(), '_nai_event_contact_phone', true);
            $event_contact_email = get_post_meta(get_the_ID(), '_nai_event_contact_email', true);
            ?>
            
            <div class="nai-event-item">
                <?php if ($event_date) : ?>
                    <div class="event-date"><?php echo esc_html($event_date); ?></div>
                <?php endif; ?>

                <h3 class="event-title">
                    <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
                </h3>
                
                <?php if ($event_start_time) : ?>
                    <div class="event-time">
                        <?php 
                        echo esc_html($event_start_time);
                        if ($event_end_time) {
                            echo ' - ' . esc_html($event_end_time);
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($event_location || $event_address || $event_city || $event_country) : ?>
                    <div class="event-location">
                        <?php if ($event_location) : ?>
                            <span class="location-name"><?php echo esc_html($event_location); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($event_address || $event_city || $event_country) : ?>
                            <span class="address">
                                <?php
                                if ($event_address) echo esc_html($event_address);
                                if ($event_city) echo ', ' . esc_html($event_city);
                                if ($event_country) echo ', ' . esc_html($event_country);
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($event_contact_name || $event_contact_phone || $event_contact_email) : ?>
                    <div class="event-contact">
                        <?php if ($event_contact_name) : ?>
                            <div class="contact-name"><?php echo esc_html($event_contact_name); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($event_contact_phone) : ?>
                            <div class="contact-phone"><?php echo esc_html($event_contact_phone); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($event_contact_email) : ?>
                            <div class="contact-email">
                                <a href="mailto:<?php echo esc_attr($event_contact_email); ?>">
                                    <?php echo esc_html($event_contact_email); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php 
endif;

wp_reset_postdata(); 