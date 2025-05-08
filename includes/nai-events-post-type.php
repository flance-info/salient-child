<?php
/**
 * Register Custom Post Type: Events
 *
 * @package    NAI_Theme
 * @subpackage Custom_Post_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class nai_Events_Post_Type {
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
    }

    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Events', 'nai' ),
            'singular_name'      => __( 'Event', 'nai' ),
            'menu_name'          => __( 'Events', 'nai' ),
            'name_admin_bar'     => __( 'Event', 'nai' ),
            'add_new'            => __( 'Add New', 'nai' ),
            'add_new_item'       => __( 'Add New Event', 'nai' ),
            'new_item'           => __( 'New Event', 'nai' ),
            'edit_item'          => __( 'Edit Event', 'nai' ),
            'view_item'          => __( 'View Event', 'nai' ),
            'all_items'          => __( 'All Events', 'nai' ),
            'search_items'       => __( 'Search Events', 'nai' ),
            'parent_item_colon'  => __( 'Parent Events:', 'nai' ),
            'not_found'          => __( 'No events found.', 'nai' ),
            'not_found_in_trash' => __( 'No events found in Trash.', 'nai' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'events' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        );

        register_post_type( 'nai_event', $args );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'nai_event_details',
            __( 'Event Details', 'nai' ),
            array( $this, 'render_event_details_meta_box' ),
            'nai_event',
            'normal',
            'high'
        );
    }

    public function render_event_details_meta_box( $post ) {
        wp_nonce_field( 'nai_save_event_details', 'nai_event_details_nonce' );
        $date = get_post_meta( $post->ID, '_nai_event_date', true );
        $start_time = get_post_meta( $post->ID, '_nai_event_start_time', true );
        $end_time = get_post_meta( $post->ID, '_nai_event_end_time', true );
        $location = get_post_meta( $post->ID, '_nai_event_location', true );
        $address = get_post_meta( $post->ID, '_nai_event_address', true );
        $city = get_post_meta( $post->ID, '_nai_event_city', true );
        $contact_name = get_post_meta( $post->ID, '_nai_event_contact_name', true );
        $contact_phone = get_post_meta( $post->ID, '_nai_event_contact_phone', true );
        $contact_email = get_post_meta( $post->ID, '_nai_event_contact_email', true );
        ?>
        <p>
            <label for="nai_event_date"><strong><?php _e( 'Event Date', 'nai' ); ?>:</strong></label><br>
            <input type="date" id="nai_event_date" name="nai_event_date" value="<?php echo esc_attr( $date ); ?>" />
        </p>
        <p>
            <label for="nai_event_start_time"><strong><?php _e( 'Start Time', 'nai' ); ?>:</strong></label><br>
            <input type="time" id="nai_event_start_time" name="nai_event_start_time" value="<?php echo esc_attr( $start_time ); ?>" />
        </p>
        <p>
            <label for="nai_event_end_time"><strong><?php _e( 'End Time', 'nai' ); ?>:</strong></label><br>
            <input type="time" id="nai_event_end_time" name="nai_event_end_time" value="<?php echo esc_attr( $end_time ); ?>" />
        </p>
        <p>
            <label for="nai_event_location"><strong><?php _e( 'Location', 'nai' ); ?>:</strong></label><br>
            <input type="text" id="nai_event_location" name="nai_event_location" value="<?php echo esc_attr( $location ); ?>" />
        </p>
        <p>
            <label for="nai_event_address"><strong><?php _e( 'Address', 'nai' ); ?>:</strong></label><br>
            <input type="text" id="nai_event_address" name="nai_event_address" value="<?php echo esc_attr( $address ); ?>" />
        </p>
        <p>
            <label for="nai_event_city"><strong><?php _e( 'City', 'nai' ); ?>:</strong></label><br>
            <input type="text" id="nai_event_city" name="nai_event_city" value="<?php echo esc_attr( $city ); ?>" />
        </p>
        <hr>
        <h4><?php _e( 'Contact Information', 'nai' ); ?></h4>
        <p>
            <label for="nai_event_contact_name"><strong><?php _e( 'Contact Name', 'nai' ); ?>:</strong></label><br>
            <input type="text" id="nai_event_contact_name" name="nai_event_contact_name" value="<?php echo esc_attr( $contact_name ); ?>" />
        </p>
        <p>
            <label for="nai_event_contact_phone"><strong><?php _e( 'Contact Phone', 'nai' ); ?>:</strong></label><br>
            <input type="text" id="nai_event_contact_phone" name="nai_event_contact_phone" value="<?php echo esc_attr( $contact_phone ); ?>" />
        </p>
        <p>
            <label for="nai_event_contact_email"><strong><?php _e( 'Contact Email', 'nai' ); ?>:</strong></label><br>
            <input type="email" id="nai_event_contact_email" name="nai_event_contact_email" value="<?php echo esc_attr( $contact_email ); ?>" />
        </p>
        <?php
    }

    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['nai_event_details_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['nai_event_details_nonce'], 'nai_save_event_details' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = array(
            'nai_event_date',
            'nai_event_start_time',
            'nai_event_end_time',
            'nai_event_location',
            'nai_event_address',
            'nai_event_city',
            'nai_event_contact_name',
            'nai_event_contact_phone',
            'nai_event_contact_email',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }
}

new nai_Events_Post_Type(); 