<?php

    /*
     * Function that adds the HTML for PayPal Standard in the payments tab from the Settings page
     *
     * @param array $options    - The saved option settings
     *
     */
    function pms_add_settings_content_paypal_standard( $options ) {

        echo '<div class="pms-payment-gateway-wrapper">';

            echo '<h4 class="pms-payment-gateway-title">' . apply_filters( 'pms_settings_page_payment_gateway_paypal_title', __( 'Paypal Standard', 'paid-member-subscriptions' ) ) . '</h4>';

            echo '<div class="pms-form-field-wrapper">';

                echo '<label class="pms-form-field-label" for="paypal-standard-email">' . __( 'PayPal E-mail Address', 'paid-member-subscriptions' ) . '</label>';
                echo '<input id="paypal-standard-email" type="text" name="pms_settings[payments][gateways][paypal_standard][email_address]" value="' . ( isset($options['payments']['gateways']['paypal_standard']['email_address']) ? $options['payments']['gateways']['paypal_standard']['email_address'] : '' ) . '" class="widefat" />';

                echo '<input type="hidden" name="pms_settings[payments][gateways][paypal_standard][name]" value="PayPal" />';

                echo '<p class="description">' . __( 'Enter your PayPal e-mail address', 'paid-member-subscriptions' ) . '</p>';

            echo '</div>';

            do_action( 'pms_settings_page_payment_gateway_paypal_extra_fields', $options );

        echo '</div>';


    }
    add_action( 'pms-settings-page_payment_gateways_content', 'pms_add_settings_content_paypal_standard' );


    /*
     * Function that send the user to the checkout page of PayPal
     *
     * @param array $user_data
     * @param int $payment_id
     * @param int $amount
     * @param array $settings
     *
     */
    function pms_process_payment_paypal_standard( $payment_data, $settings ) {

        // Do nothing if the payment id wasn't sent
        if( $payment_data['payment_id'] === false )
            return;


        //Update payment type
        $payment = pms_get_payment( $payment_data['payment_id'] );
        $payment->update_type( apply_filters( 'pms_paypal_standard_payment_type', 'web_accept_paypal_standard', $payment_data, $settings ) );


        // Set the notify URL
        $notify_url = home_url() . '?pay_gate_listener=paypal_ipn';


        if( isset( $settings['test_mode'] ) )
            $paypal_link = 'https://www.sandbox.paypal.com/cgi-bin/webscr/?';
        else
            $paypal_link = 'https://www.paypal.com/cgi-bin/webscr/?';

        $paypal_args = array(
            'cmd'           => '_xclick',
            'business'      => trim( $settings['gateways']['paypal_standard']['email_address'] ),
            'email'         => $payment_data['user_data']['user_email'],
            'item_name'     => $payment_data['user_data']['subscription']->name,
            'item_number'   => $payment_data['user_data']['subscription']->id,
            'currency_code' => trim( $settings['currency'] ),
            'amount'        => $payment_data['amount'],
            'tax'           => 0,
            'custom'        => $payment_data['payment_id'],
            'notify_url'    => $notify_url,
            'return'        => add_query_arg( array( 'pms_gateway_payment_id' => base64_encode($payment_data['payment_id']), 'pmsscscd' => base64_encode('subscription_plans') ), $payment_data['redirect_url'] ),
            'charset'       => 'UTF-8'
        );

        $paypal_link .= http_build_query( apply_filters( 'pms_paypal_standard_args', $paypal_args, $payment_data, $settings ) );


        do_action( 'pms_before_paypal_redirect', $paypal_link, $payment_data, $settings );


        // Redirect only if tkn is set
        if( isset( $_POST['pmstkn'] ) ) {

            header( 'Location:' . $paypal_link );
            exit;
        }

    }
    add_action( 'pms_process_payment_paypal_standard', 'pms_process_payment_paypal_standard', 10, 2 );


    /*
     * Processes the IPN from PayPal and update information based on the IPN data
     *
     */
    function pms_paypal_ipn_listener() {

        if( !isset( $_GET['pay_gate_listener'] ) || $_GET['pay_gate_listener'] != 'paypal_ipn' )
            return;

        // Get settings
        $settings = get_option( 'pms_settings' );


        $ipn_listener = new PMS_IpnListener();

        if( isset( $settings['payments']['test_mode'] ) )
            $ipn_listener->use_sandbox = true;


        $verified = false;

        // Process the IPN
        try {
            $ipn_listener->requirePostMethod();
            $verified = $ipn_listener->processIpn();
        } catch ( Exception $e ) {

        }


        if( $verified ) {

            $post_data = $_POST;

            // Get user id and payment id from custom variable sent by IPN
            $payment_id = isset( $post_data['custom'] ) ? $post_data['custom'] : 0;

            // Get the payment
            $payment = pms_get_payment( $payment_id );

            // Get user id from the payment
            $user_id = $payment->user_id;

            $payment_data = apply_filters( 'pms_paypal_ipn_payment_data', array(
                'payment_id'     => $payment_id,
                'user_id'        => $user_id,
                'type'           => $post_data['txn_type'],
                'status'         => strtolower($post_data['payment_status']),
                'transaction_id' => $post_data['txn_id'],
                'amount'         => $post_data['mc_gross'],
                'date'           => $post_data['payment_date'],
                'subscription_id'=> $post_data['item_number']
            ), $post_data );


            // web_accept is returned for A Direct Credit Card (Pro) transaction,
            // A Buy Now, Donation or Smart Logo for eBay auctions button
            if( $payment_data['type'] == 'web_accept' ) {

                // If the payment has already been completed do nothing
                if( $payment->status == 'completed' )
                    return;

                // If the status is completed update the payment and also activate the member subscriptions
                if( $payment_data['status'] == 'completed' ) {

                    // Complete payment
                    $payment->update( array( 'status' => $payment_data['status'], 'transaction_id' => $payment_data['transaction_id'] ) );

                    // Update member subscriptions
                    $member = pms_get_member( $payment_data['user_id'] );


                    // Update status to active for subscriptions that exist both in the user subscriptions and also in the payment info
                    foreach( $member->subscriptions as $member_subscription ) {
                        if( $member_subscription['subscription_plan_id'] == $payment_data['subscription_id'] ) {

                            // If subscription is pending it is a new one
                            if( $member_subscription['status'] == 'pending' ) {
                                $member_subscription_expiration_date = $member_subscription['expiration_date'];

                            // This is an old subscription
                            } else {

                                $subscription_plan = pms_get_subscription_plan( $member_subscription['subscription_plan_id'] );

                                if( strtotime( $member_subscription['expiration_date'] ) < time() || $subscription_plan->duration === 0 )
                                    $member_subscription_expiration_date = $subscription_plan->get_expiration_date();
                                else
                                    $member_subscription_expiration_date = date( 'Y-m-d 23:59:59', strtotime( $member_subscription['expiration_date'] . '+' . $subscription_plan->duration . ' ' . $subscription_plan->duration_unit ) );
                            }

                            // Update subscription
                            $member->update_subscription( $member_subscription['subscription_plan_id'], $member_subscription['start_date'], $member_subscription_expiration_date, 'active' );

                        }
                    }

                    /*
                     * If the subscription plan id sent by the IPN is not found in the members subscriptions
                     * then it could be an update to an existing one
                     *
                     * If one of the member subscriptions is in the same group as the payment subscription id,
                     * the payment subscription id is an upgrade to the member subscription one
                     *
                     */
                    if( !in_array( $payment_data['subscription_id'], $member->get_subscriptions_ids() ) ) {

                        $group_subscription_plans = pms_get_subscription_plans_group( $payment_data['subscription_id'], false );

                        if( count($group_subscription_plans) > 1 ) {

                            // Get current member subscription that will be upgraded
                            foreach( $group_subscription_plans as $subscription_plan ) {
                                if( in_array( $subscription_plan->id, $member->get_subscriptions_ids() ) ) {
                                    $member_subscription = $subscription_plan;
                                    break;
                                }
                            }

                            if( isset($member_subscription) ) {

                                do_action( 'pms_paypal_web_accept_before_upgrade_subscription', $member_subscription->id, $payment_data, $post_data );

                                $member->remove_subscription( $member_subscription->id );

                                $new_subscription_plan = pms_get_subscription_plan( $payment_data['subscription_id'] );

                                $member->add_subscription( $new_subscription_plan->id, date('Y-m-d 23:59:59'), $new_subscription_plan->get_expiration_date(), 'active' );

                                do_action( 'pms_paypal_web_accept_after_upgrade_subscription', $member_subscription->id, $payment_data, $post_data );
                            }

                        }

                    }

                // If payment status is not complete, something happened, so log it in the payment
                } else {

                    // Add the transaction ID
                    $payment->update( array( 'transaction_id' => $payment_data['transaction_id'] ) );

                    $log_data = array(
                        'payment_status' => $post_data['payment_status'],
                        'payment_date'   => $post_data['payment_date'],
                        'payer_id'       => $post_data['payer_id'],
                        'payer_email'    => $post_data['payer_email'],
                        'payer_status'   => $post_data['payer_status']
                    );

                    $payment->add_log_entry( 'failure', __( 'The payment could not be completed successfully', 'paid-member-subscription' ), $log_data );

                }

            }

            do_action( 'pms_paypal_ipn_listener_verified', $payment_data, $post_data );

        }

    }
    add_action( 'init', 'pms_paypal_ipn_listener' );