<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

interface CUAR_PaymentGateway
{

    //-- General functions ------------------------------------------------------------------------------------------------------------------------------------/

    /**
     * @return string The gateway's unique ID
     */
    function get_id();

    /**
     * @return string The gateway's name
     */
    function get_name();

    /**
     * @return bool Is the gateway enabled
     */
    function is_enabled();

    /**
     * Process the payment which has been created with the pending status
     *
     * @param CUAR_Payment $payment        The payment object
     * @param array        $payment_data   The payment data as submitted in the checkout form
     * @param array        $gateway_params The parameters from the gateway form
     */
    function process_payment($payment, $payment_data, $gateway_params);

    //-- UI functions -----------------------------------------------------------------------------------------------------------------------------------------/

    /**
     * @return array('icon', 'link')
     */
    function get_icon();

    /**
     * @return bool
     */
    function has_form();

    /**
     *
     */
    function print_form();

    //-- Settings functions -----------------------------------------------------------------------------------------------------------------------------------/

    /**
     * Add our fields to the settings tab
     */
    function print_settings();

    /**
     * Validate our options
     *
     * @param CUAR_Settings $cuar_settings
     * @param array         $input
     * @param array         $validated
     *
     * @return array
     */
    function validate_options($validated, $cuar_settings, $input);

}