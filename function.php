//Function to make custom post type from publish to draft arcording to end_date field.
function delete_expired_events_callback() {
    $args = array(
        'post_type' => 'events',
        'post_status'    => 'publish',
        'posts_per_page' => -1
    );

    $events = new WP_Query($args);
    if ($events->have_posts()):
        while($events->have_posts()): $events->the_post();    

            $end_date = get_post_meta( get_the_ID(), 'end_date', true );
            $end_date_time = strtotime($end_date);

            if ($end_date_time < time()) {
                //to move post to trash
                //wp_trash_post(get_the_ID());
                
                //set post to draft
                  $my_post = array(
                      'ID'           => get_the_ID(),
                      'post_status'   => 'draft',
                  );

                // Update the post into the database
                  wp_update_post( $my_post );
            }
        endwhile;
    endif;
}

add_action( 'wp', 'delete_expired_events_daily' );
add_action( 'delete_expired_events', 'delete_expired_events_callback' );

//Do cron job everyday.
function delete_expired_events_daily() {
    if ( !wp_next_scheduled( 'delete_expired_events' ) ) {
        wp_schedule_event( strtotime( '1am tomorrow' ), 'daily', 'delete_expired_events' );
    }
}
