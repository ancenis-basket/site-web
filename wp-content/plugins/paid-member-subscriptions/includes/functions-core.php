<?php
/*
 * Core functions
 *
 */


    /*
     * Function that is a wrapper for default WP function get_post_meta,
     * but if provided only the $post_id will return an associative array with values,
     * not an associative array of array
     *
     * @param $post_id      - the ID of the post
     * @param $key          - the post meta key
     * @param $single
     *
     */
    function pms_get_post_meta( $post_id, $key = '', $single = false ) {

        if( empty( $key ) ) {
            $post_meta = get_post_meta( $post_id );

            foreach( $post_meta as $key => $value ) {
                $post_meta[$key] = $value[0];
            }

            return $post_meta;
        }

        return get_post_meta( $post_id, $key, $single );

    }


    /*
     * Function that returns all the users that are not members yet
     *
     * @return array
     *
     */
    function pms_get_users_non_members() {

        global $wpdb;

        $results = $wpdb->get_results( "SELECT ID FROM {$wpdb->users} LEFT JOIN {$wpdb->prefix}pms_member_subscriptions ON {$wpdb->users}.ID = {$wpdb->prefix}pms_member_subscriptions.user_id WHERE {$wpdb->prefix}pms_member_subscriptions.user_id is null", ARRAY_A );

        $users = array();

        if( !empty( $results ) ) {
            foreach( $results as $result ) {
                $users[] = new WP_User( $result['ID'] );
            }
        }

        return $users;

    }


    /*
     * Function that returns only the date part of a date-time format
     *
     * @param string $date
     *
     * @return string
     *
     */
    function pms_sanitize_date( $date ) {

        if( !isset( $date ) )
            return;

        $date_time = explode( ' ', $date );

        return $date_time[0];

    }


    /*
     * Handles errors in the front end
     *
     */
    function pms_errors() {
        static $wp_errors;

        return ( isset($wp_errors) ? $wp_errors : ( $wp_errors = new WP_Error( null, null, null ) ) );
    }


    /*
     * Handles success messages in front end
     *
     */
    function pms_success() {
        static $pms_success;

        return ( isset($pms_success) ? $pms_success : ( $pms_success = new PMS_Success( null, null ) ) );
    }


    /*
     * Checks to see if there are any success messages somewhere in the
     * URL and add them to the success object
     *
     */
    function pms_check_request_args_success_messages() {

        if( !isset($_REQUEST) )
            return;

        // If there is a success message in the request add it directly
        if( isset( $_REQUEST['pmsscscd'] ) && isset( $_REQUEST['pmsscsmsg'] ) ) {

            $message_code = esc_attr( base64_decode( trim($_REQUEST['pmsscscd']) ) );
            $message      = esc_attr( base64_decode( trim($_REQUEST['pmsscsmsg']) ) );

            pms_success()->add( $message_code, $message );

        // If there is no message, but the code is present check to see for a gateway action present
        // and add messages
        } elseif( isset( $_REQUEST['pmsscscd'] ) && !isset( $_REQUEST['pmsscsmsg'] ) ) {

            $message_code = esc_attr( base64_decode( trim($_REQUEST['pmsscscd']) ) );

            if( !isset( $_REQUEST['pms_gateway_payment_action'] ) )
                return;

            $payment_action = esc_attr( base64_decode( trim( $_REQUEST['pms_gateway_payment_action'] ) ) );

            if( isset( $_REQUEST['pms_gateway_payment_id'] ) ) {

                $payment_id = esc_attr( base64_decode( trim( $_REQUEST['pms_gateway_payment_id'] ) ) );
                $payment    = pms_get_payment( $payment_id );

                // If status of the payment is completed add a success message
                if( $payment->status == 'completed' ) {

                    if( $payment_action == 'upgrade_subscription' )
                        pms_success()->add( $message_code, apply_filters( 'pms_message_gateway_payment_action', __( 'Congratulations, you have successfully upgraded your subscription.', 'paid-member-subscriptions' ), $payment->status, $payment_action ) );

                    elseif( $payment_action == 'renew_subscription' )
                        pms_success()->add( $message_code, apply_filters( 'pms_message_gateway_payment_action', __( 'Congratulations, you have successfully renewed your subscription.', 'paid-member-subscriptions' ), $payment->status, $payment_action ) );

                } elseif( $payment->status == 'pending' ) {

                    if( $payment_action == 'upgrade_subscription' )
                        pms_success()->add( $message_code, apply_filters( 'pms_message_gateway_payment_action', __( 'Thank you for your payment. The upgrade may take a while to be processed.', 'paid-member-subscriptions' ), $payment->status, $payment_action ) );

                    elseif( $payment_action == 'renew_subscription' )
                        pms_success()->add( $message_code, apply_filters( 'pms_message_gateway_payment_action', __( 'Thank you for your payment. The renew may take a while to be processed.', 'paid-member-subscriptions' ), $payment->status, $payment_action ) );

                }

            }

        }

    }
    add_action( 'init', 'pms_check_request_args_success_messages' );


    /*
     * Function that echoes the errors of a field
     *
     * @param array $field_errors - an array containing the errors
     *
     */
    function pms_display_field_errors( $field_errors = array(), $return = false ) {

        $output = '';

        if( !empty( $field_errors ) ) {
            $output = '<div class="pms_field-errors-wrapper">';

            foreach( $field_errors as $field_error ) {
                $output .= '<p>' . $field_error . '</p>';
            }

            $output .= '</div>';
        }

        if( $return )
            return $output;
        else
            echo $output;

    }


    /*
     * Function that echoes success messages
     *
     * @param array $messages - an array containing the messages
     *
     */
    function pms_display_success_messages( $messages = array(), $return = false ) {

        $output = '';

        if( !empty( $messages ) ) {
            $output = '<div class="pms_success-messages-wrapper">';

            foreach( $messages as $message ) {
                $output .= '<p>' . $message . '</p>';
            }

            $output .= '</div>';
        }

        if( $return )
            return $output;
        else
            echo $output;

    }


    /*
     * Returns an array with the currency codes and their names
     *
     * @return array
     *
     */
    function pms_get_currencies() {

        $currencies = array(
            'USD'   => __( 'US Dollar', 'paid-member-subscriptions' ),
            'EUR'   => __( 'Euro', 'paid-member-subscriptions' ),
            'GBP'   => __( 'Pound Sterling', 'paid-member-subscriptions' ),
            'CAD'   => __( 'Canadian Dollar', 'paid-member-subscriptions' ),
            'AUD'   => __( 'Australian Dollar', 'paid-member-subscriptions' ),
            'BRL'   => __( 'Brazilian Real', 'paid-member-subscriptions' ),
            'CZK'   => __( 'Czech Koruna', 'paid-member-subscriptions' ),
            'DKK'   => __( 'Danish Krone', 'paid-member-subscriptions' ),
            'HKD'   => __( 'Hong Kong Dollar', 'paid-member-subscriptions' ),
            'HUF'   => __( 'Hungarian Forint', 'paid-member-subscriptions' ),
            'ILS'   => __( 'Israeli New Sheqel', 'paid-member-subscriptions' ),
            'JPY'   => __( 'Japanese Yen', 'paid-member-subscriptions' ),
            'MYR'   => __( 'Malaysian Ringgit', 'paid-member-subscriptions' ),
            'MXN'   => __( 'Mexican Peso', 'paid-member-subscriptions' ),
            'NOK'   => __( 'Norwegian Krone', 'paid-member-subscriptions' ),
            'NZD'   => __( 'New Zealand Dollar', 'paid-member-subscriptions' ),
            'PHP'   => __( 'Philippine Peso', 'paid-member-subscriptions' ),
            'PLN'   => __( 'Polish Zloty', 'paid-member-subscriptions' ),
            'RUB'   => __( 'Russian Ruble', 'paid-member-subscriptions' ),
            'SGD'   => __( 'Singapore Dollar', 'paid-member-subscriptions' ),
            'SEK'   => __( 'Swedish Krona', 'paid-member-subscriptions' ),
            'CHF'   => __( 'Swiss Franc', 'paid-member-subscriptions' ),
            'TWD'   => __( 'Taiwan New Dollar', 'paid-member-subscriptions' ),
            'THB'   => __( 'Thai Baht', 'paid-member-subscriptions' ),
            'TRY'   => __( 'Turkish Lira', 'paid-member-subscriptions' )
        );

        return apply_filters( 'pms_currencies', $currencies );

    }

    /*
     * Given a currency code returns a string with the currency symbol as HTML entity
     *
     * @return string
     *
     */
    function pms_get_currency_symbol( $currency_code )
    {

        $currencies = apply_filters('pms_currency_symbols',
            array(
                'AED' => '&#1583;.&#1573;', // ?
                'AFN' => '&#65;&#102;',
                'ALL' => '&#76;&#101;&#107;',
                'AMD' => '',
                'ANG' => '&#402;',
                'AOA' => '&#75;&#122;', // ?
                'ARS' => '&#36;',
                'AUD' => '&#36;',
                'AWG' => '&#402;',
                'AZN' => '&#1084;&#1072;&#1085;',
                'BAM' => '&#75;&#77;',
                'BBD' => '&#36;',
                'BDT' => '&#2547;', // ?
                'BGN' => '&#1083;&#1074;',
                'BHD' => '.&#1583;.&#1576;', // ?
                'BIF' => '&#70;&#66;&#117;', // ?
                'BMD' => '&#36;',
                'BND' => '&#36;',
                'BOB' => '&#36;&#98;',
                'BRL' => '&#82;&#36;',
                'BSD' => '&#36;',
                'BTN' => '&#78;&#117;&#46;', // ?
                'BWP' => '&#80;',
                'BYR' => '&#112;&#46;',
                'BZD' => '&#66;&#90;&#36;',
                'CAD' => '&#36;',
                'CDF' => '&#70;&#67;',
                'CHF' => '&#67;&#72;&#70;',
                'CLF' => '', // ?
                'CLP' => '&#36;',
                'CNY' => '&#165;',
                'COP' => '&#36;',
                'CRC' => '&#8353;',
                'CUP' => '&#8396;',
                'CVE' => '&#36;', // ?
                'CZK' => '&#75;&#269;',
                'DJF' => '&#70;&#100;&#106;', // ?
                'DKK' => '&#107;&#114;',
                'DOP' => '&#82;&#68;&#36;',
                'DZD' => '&#1583;&#1580;', // ?
                'EGP' => '&#163;',
                'ETB' => '&#66;&#114;',
                'EUR' => '&#8364;',
                'FJD' => '&#36;',
                'FKP' => '&#163;',
                'GBP' => '&#163;',
                'GEL' => '&#4314;', // ?
                'GHS' => '&#162;',
                'GIP' => '&#163;',
                'GMD' => '&#68;', // ?
                'GNF' => '&#70;&#71;', // ?
                'GTQ' => '&#81;',
                'GYD' => '&#36;',
                'HKD' => '&#36;',
                'HNL' => '&#76;',
                'HRK' => '&#107;&#110;',
                'HTG' => '&#71;', // ?
                'HUF' => '&#70;&#116;',
                'IDR' => '&#82;&#112;',
                'ILS' => '&#8362;',
                'INR' => '&#8377;',
                'IQD' => '&#1593;.&#1583;', // ?
                'IRR' => '&#65020;',
                'ISK' => '&#107;&#114;',
                'JEP' => '&#163;',
                'JMD' => '&#74;&#36;',
                'JOD' => '&#74;&#68;', // ?
                'JPY' => '&#165;',
                'KES' => '&#75;&#83;&#104;', // ?
                'KGS' => '&#1083;&#1074;',
                'KHR' => '&#6107;',
                'KMF' => '&#67;&#70;', // ?
                'KPW' => '&#8361;',
                'KRW' => '&#8361;',
                'KWD' => '&#1583;.&#1603;', // ?
                'KYD' => '&#36;',
                'KZT' => '&#1083;&#1074;',
                'LAK' => '&#8365;',
                'LBP' => '&#163;',
                'LKR' => '&#8360;',
                'LRD' => '&#36;',
                'LSL' => '&#76;', // ?
                'LTL' => '&#76;&#116;',
                'LVL' => '&#76;&#115;',
                'LYD' => '&#1604;.&#1583;', // ?
                'MAD' => '&#1583;.&#1605;.', //?
                'MDL' => '&#76;',
                'MGA' => '&#65;&#114;', // ?
                'MKD' => '&#1076;&#1077;&#1085;',
                'MMK' => '&#75;',
                'MNT' => '&#8366;',
                'MOP' => '&#77;&#79;&#80;&#36;', // ?
                'MRO' => '&#85;&#77;', // ?
                'MUR' => '&#8360;', // ?
                'MVR' => '.&#1923;', // ?
                'MWK' => '&#77;&#75;',
                'MXN' => '&#36;',
                'MYR' => '&#82;&#77;',
                'MZN' => '&#77;&#84;',
                'NAD' => '&#36;',
                'NGN' => '&#8358;',
                'NIO' => '&#67;&#36;',
                'NOK' => '&#107;&#114;',
                'NPR' => '&#8360;',
                'NZD' => '&#36;',
                'OMR' => '&#65020;',
                'PAB' => '&#66;&#47;&#46;',
                'PEN' => '&#83;&#47;&#46;',
                'PGK' => '&#75;', // ?
                'PHP' => '&#8369;',
                'PKR' => '&#8360;',
                'PLN' => '&#122;&#322;',
                'PYG' => '&#71;&#115;',
                'QAR' => '&#65020;',
                'RON' => '&#108;&#101;&#105;',
                'RSD' => '&#1044;&#1080;&#1085;&#46;',
                'RUB' => '&#1088;&#1091;&#1073;',
                'RWF' => '&#1585;.&#1587;',
                'SAR' => '&#65020;',
                'SBD' => '&#36;',
                'SCR' => '&#8360;',
                'SDG' => '&#163;', // ?
                'SEK' => '&#107;&#114;',
                'SGD' => '&#36;',
                'SHP' => '&#163;',
                'SLL' => '&#76;&#101;', // ?
                'SOS' => '&#83;',
                'SRD' => '&#36;',
                'STD' => '&#68;&#98;', // ?
                'SVC' => '&#36;',
                'SYP' => '&#163;',
                'SZL' => '&#76;', // ?
                'THB' => '&#3647;',
                'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
                'TMT' => '&#109;',
                'TND' => '&#1583;.&#1578;',
                'TOP' => '&#84;&#36;',
                'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
                'TTD' => '&#36;',
                'TWD' => '&#78;&#84;&#36;',
                'TZS' => '',
                'UAH' => '&#8372;',
                'UGX' => '&#85;&#83;&#104;',
                'USD' => '&#36;',
                'UYU' => '&#36;&#85;',
                'UZS' => '&#1083;&#1074;',
                'VEF' => '&#66;&#115;',
                'VND' => '&#8363;',
                'VUV' => '&#86;&#84;',
                'WST' => '&#87;&#83;&#36;',
                'XAF' => '&#70;&#67;&#70;&#65;',
                'XCD' => '&#36;',
                'XDR' => '',
                'XOF' => '',
                'XPF' => '&#70;',
                'YER' => '&#65020;',
                'ZAR' => '&#82;',
                'ZMK' => '&#90;&#75;', // ?
                'ZWL' => '&#90;&#36;',
            )
        );


        $currency_symbol = ( isset( $currencies[$currency_code] ) ) ? $currencies[$currency_code] : '';

        return $currency_symbol;

    }


    /*
     * Function that returns the current user id or the current user that is edited in front-end
     * edit profile when an admin is editing
     *
     * @return int
     *
     */
    function pms_get_current_user_id() {
        if( isset( $_GET['edit_user'] ) && !empty( $_GET['edit_user'] ) && current_user_can('edit_users') )
            return trim( $_GET['edit_user'] );
        else
            return get_current_user_id();
    }


    /*
     * Returns the url of the current page
     *
     * @param bool $strip_query_args - whether to eliminate query arguments from the url or not
     *
     * @return string
     *
     */
    function pms_get_current_page_url( $strip_query_args = false ) {

        $page_url = 'http';

        if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
            $page_url .= "s";

        $page_url .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
            $page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        else
            $page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];


        // Remove query arguments
        if( $strip_query_args ) {
            $page_url_parts = explode( '?', $page_url );

            $page_url = $page_url_parts[0];

            // Keep query args "p" and "page_id" for non-beautified permalinks
            if( isset( $page_url_parts[1] ) ) {
                $page_url_query_args = explode( '&', $page_url_parts[1] );

                if( !empty( $page_url_query_args ) ) {
                    foreach( $page_url_query_args as $key => $query_arg ) {

                        if( strpos( $query_arg, 'p=' ) === 0 ) {
                            $query_arg_parts = explode( '=', $query_arg );
                            $query_arg       = $query_arg_parts[0];
                            $query_arg_val   = $query_arg_parts[1];

                            $page_url = add_query_arg( array( $query_arg => $query_arg_val ), $page_url );
                        }

                        if( strpos( $query_arg, 'page_id=' ) === 0 ) {
                            $query_arg_parts = explode( '=', $query_arg );
                            $query_arg       = $query_arg_parts[0];
                            $query_arg_val   = $query_arg_parts[1];

                            $page_url = add_query_arg( array( $query_arg => $query_arg_val ), $page_url );
                        }

                    }
                }
            }

        }

        if ( function_exists('apply_filters') ) apply_filters( 'pms_get_current_page_url', $page_url );

        return $page_url;

    }


    /*
     * Checks if there is a need to add the http:// prefix to a link and adds it. Returns the correct link.
     *
     * @return string
     *
     */
    function pms_add_missing_http( $link ) {
        $http = '';

        if ( preg_match( '#^(?:[a-z\d]+(?:-+[a-z\d]+)*\.)+[a-z]+(?::\d+)?(?:/|$)#i', $link ) ) { //if missing http(s)

            $http = 'http';
            if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
                $http .= "s";
            $http .= "://";
        }

        return $http . $link;

    }


    /*
     * Modify the logout url to redirect to current page if the user is in the front-end
     * and logs out
     *
     */
    function pms_logout_redirect_url( $logout_url, $redirect ) {

        $current_page = pms_get_current_page_url();

        // Do nothing if we're in an admin page
        if( strpos( $current_page, 'wp-admin' ) !== false )
            return $logout_url;

        $logout_url = add_query_arg( array( 'redirect_to' => urlencode( esc_url( $current_page ) ) ), $logout_url );

        return $logout_url;

    }
    add_filter( 'logout_url', 'pms_logout_redirect_url', 10, 2 );


    /*
     *
     * Add a notice if people are not able to register via Paid Member Subscriptions; Tell them to check Membership -> "Anyone can register" checkbox under Settings -> General
     *
     * */
    if ( ( get_option('users_can_register') == false) && ( !class_exists('WPPB_Add_General_Notices') ) ) {
        if (is_multisite()) {
            new PMS_Add_General_Notices('pms_anyone_can_register',
                sprintf(__('To allow users to register via Paid Member Subscriptions, you first must enable user registration. Go to %1$sNetwork Settings%2$s, and under Registration Settings make sure to check “User accounts may be registered”. %3$sDismiss%4$s', 'paid-member-subscriptions'), "<a href='" . network_admin_url('settings.php') . "'>", "</a>", "<a href='" . esc_url(add_query_arg('pms_anyone_can_register_dismiss_notification', '0')) . "'>", "</a>"),
                'update-nag');
        } else {
            new PMS_Add_General_Notices('pms_anyone_can_register',
                sprintf(__('To allow users to register via Paid Member Subscriptions, you first must enable user registration. Go to %1$sSettings -> General%2$s tab, and under Membership make sure to check “Anyone can register”. %3$sDismiss%4$s', 'paid-member-subscriptions'), "<a href='" . admin_url('options-general.php') . "'>", "</a>", "<a href='" . esc_url(add_query_arg('pms_anyone_can_register_dismiss_notification', '0')) . "'>", "</a>"),
                'update-nag');
        }
    }