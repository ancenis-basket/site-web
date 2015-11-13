<?php

/*
 * Functions for member things
 *
 */


    /*
     * Wrapper function to return a member object
     *
     * @param $user_id  - The id of the user we wish to return
     *
     * @return PMS_Member
     *
     */
    function pms_get_member( $user_id ) {
        return new PMS_Member( $user_id );
    }


    /*
     * Queries the database for user ids that also match the member_subscriptions table
     * and returns an array with member objects
     *
     * @param array $args   - arguments to modify the query and return different results
     *
     * @param array         - array with member objects
     *
     */
    function pms_get_members( $args = array() ) {

        global $wpdb;

        $defaults = array(
            'order'                => 'ASC',
            'orderby'              => 'ID',
            'offset'               => '',
            'number'               => '',
            'subscription_plan_id' => '',
            'search'               => ''
        );

        $args = wp_parse_args( $args, $defaults );

        // Start query string
        $query_string       = "SELECT DISTINCT users.ID ";

        // Query string sections
        $query_from         = "FROM {$wpdb->users} users ";
        $query_inner_join   = "INNER JOIN {$wpdb->prefix}pms_member_subscriptions member_subscriptions ON users.ID = member_subscriptions.user_id ";
        $query_where        = "WHERE 1=%d ";

        if( !empty($args['subscription_plan_id']) )
            $query_where    = $query_where . " AND member_subscriptions.subscription_plan_id = " . $args['subscription_plan_id'] . " ";

        // Add search query
        if( !empty($args['search']) ) {
            $search_term = $args['search'];
            $query_where    = $query_where . " AND  " . "  users.user_email LIKE '%%%s%%' OR users.user_nicename LIKE '%%%s%%'  ". " ";
        }

        $query_oder_by      = "ORDER BY users." . $args['orderby'] . ' ';

        $query_limit        = '';
        if( $args['number'] )
            $query_limit    = 'LIMIT ' . $args['number'] . ' ';

        $query_offset       = '';
        if( $args['offset'] )
            $query_offset   = 'OFFSET ' . $args['offset'] . ' ';

        // Concatenate query string
        $query_string .= $query_from . $query_inner_join . $query_where . $query_oder_by . $query_limit . $query_offset;

        // Return results
        if (!empty($search_term))
            $results = $wpdb->get_results( $wpdb->prepare( $query_string, 1, $wpdb->esc_like( $search_term ) , $wpdb->esc_like( $search_term ) ), ARRAY_A );
        else
            $results = $wpdb->get_results( $wpdb->prepare( $query_string, 1 ), ARRAY_A );

        // Get members for each ID passed
        $members = array();
        if (!empty($results)) {
            foreach ($results as $user_data) {
                $member = new PMS_Member($user_data['ID']);

                $members[] = $member;
            }
        }

        return $members;

    }


    /*
     * Function that returns all possible member statuses
     *
     * @return array
     *
     */
    function pms_get_member_statuses() {

        return apply_filters( 'pms_member_statuses', array(
            'active'    => __( 'Active', 'paid-member-subscriptions' ),
            'canceled'  => __( 'Canceled', 'paid-member-subscriptions' ),
            'expired'   => __( 'Expired', 'paid-member-subscriptions' ),
            'pending'   => __( 'Pending', 'paid-member-subscriptions' )
        ));

    }



    /*
     * Function triggered by the cron job that checks for any expired subscriptions.
     *
     * @return void
     *
     */
    function pms_member_check_expired_subscriptions() {
        // check if any subscriptions have expired and change their status to expired
        $members = pms_get_members();

        if ( !empty( $members ) ) {
            foreach ( $members as $member ) {
                foreach ( $member->subscriptions as $subscription ) {
                    if ( ( $subscription['status'] == 'active' ) && ((strtotime( current_time('mysql')) ) >  ( strtotime($subscription['expiration_date']))) )
                        $member->update_subscription( $subscription['subscription_plan_id'], $subscription['start_date'], $subscription['expiration_date'], 'expired');
                }
            }
        }
    }

    /**
     * Function that retrieves the unique user key from the database. If we don't have one we generate one and add it to the database
     *
     * @param string $requested_user_login the user login
     *
     */
    function pms_retrieve_activation_key( $requested_user_login ){
        global $wpdb;

        $key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $requested_user_login ) );

        if ( empty( $key ) ) {

            // Generate something random for a key...
            $key = wp_generate_password( 20, false );
            do_action('pms_retrieve_password_key', $requested_user_login, $key);

            // Now insert the new md5 key into the db
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $requested_user_login));
        }

        return $key;
    }

    /**
     * Function triggered by the cron job that removes the user activation key (used for password reset) from the db, (make it expire) every 20 hours (72000 seconds).
     *
     */
    function pms_remove_expired_activation_key(){
        $activation_keys = get_option( 'pms_recover_password_activation_keys', array());

        if ( !empty($activation_keys) ) { //option exists

            foreach ($activation_keys as $id => $activation_key) {

                if ( ( $activation_key['time'] + 72000 ) < time() ) {
                    update_user_meta($id, 'user_activation_key', '' ); // remove expired activation key from db
                    unset($activation_keys[$id]);
                    update_option('pms_recover_password_activation_keys', $activation_keys); // delete activation key from option
                }

            }

        }
    }
