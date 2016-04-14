<?php

/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

class CUAR_PaymentsEditorHelper
{
    /** @var CUAR_Plugin */
    private $plugin;

    /** @var CUAR_PaymentsAddOn */
    private $pa_addon;

    /**
     * Constructor
     */
    public function __construct($plugin, $pa_addon)
    {
        $this->plugin = $plugin;
        $this->pa_addon = $pa_addon;
    }

    public function print_data_fields($payment_id)
    {
        $payment = new CUAR_Payment($payment_id);

        $template_suffix = is_admin() ? '-admin' : '-frontend';
        include($this->plugin->get_template_file_path(
            CUAR_INCLUDES_DIR . '/core-addons/payments',
            array(
                'payment-editor-data' . $template_suffix . '.template.php',
                'payment-editor-data.template.php',
            ),
            'templates'));
    }
    
    public function print_gateway_fields($payment_id) 
    {
        $payment = new CUAR_Payment($payment_id);
        $gateways = $this->pa_addon->settings()->get_available_gateways();

        $template_suffix = is_admin() ? '-admin' : '-frontend';
        include($this->plugin->get_template_file_path(
            CUAR_INCLUDES_DIR . '/core-addons/payments',
            array(
                'payment-editor-gateway' . $template_suffix . '.template.php',
                'payment-editor-gateway.template.php',
            ),
            'templates'));
    }

    /**
     * Print the payment items manager
     */
    public function print_notes_manager($payment_id)
    {
        $this->pa_addon->enqueue_scripts();

        $payment = new CUAR_Payment($payment_id);
        $notes = $payment->get_notes();

        $template_suffix = is_admin() ? '-admin' : '-frontend';
        $item_template = $this->plugin->get_template_file_path(
            CUAR_INCLUDES_DIR . '/core-addons/payments',
            array(
                'payment-editor-note-list-item' . $template_suffix . '.template.php',
                'payment-editor-note-list-item.template.php',
            ),
            'templates');

        include($this->plugin->get_template_file_path(
            CUAR_INCLUDES_DIR . '/core-addons/payments',
            array(
                'payment-editor-note-list' . $template_suffix . '.template.php',
                'payment-editor-note-list.template.php',
            ),
            'templates'));
    }

    /**
     * Print the input fields corresponding to the payment billing address
     *
     * @param int $payment_id
     */
    public function print_address_fields($payment_id)
    {
        $this->pa_addon->enqueue_scripts();

        $payment = new CUAR_Payment($payment_id);
        $address = $payment->get_address();

        /** @var CUAR_AddressesAddOn $am */
        $am = $this->plugin->get_addon('address-manager');
        $am->print_address_editor($address,
            'cuar_address', '',
            array(), '',
            'metabox');
    }

    /**
     * Save the billing information
     *
     * @param int   $payment_id The ID of the payment
     * @param array $form_data  The form data (typically $_POST)
     */
    public function save_address_fields($payment_id, $form_data)
    {
        $address = isset($form_data['cuar_address']) ? $form_data['cuar_address'] : array();

        $payment = new CUAR_Payment($payment_id, false);
        $payment->set_address($address);
    }

    /**
     * Save the payment gateway properties
     *
     * @param int   $payment_id The ID of the payment
     * @param array $form_data  The form data (typically $_POST)
     */
    public function save_gateway_fields($payment_id, $form_data)
    {
    }

    /**
     * Save the general payment properties
     *
     * @param int   $payment_id The ID of the payment
     * @param array $form_data  The form data (typically $_POST)
     */
    public function save_data_fields($payment_id, $form_data)
    {
        $data = isset($form_data['data']) ? $form_data['data'] : array();
        $currency = isset($data['currency']) ? $data['currency'] : 'EUR';
        $status = isset($data['status']) ? $data['status'] : '';
        $date = isset($data['date']) ? $data['date'] : '';
        $amount = isset($data['amount']) ? $data['amount'] : 0;

        $payment = new CUAR_Payment($payment_id, false);
        $payment->set_currency($currency);
        $payment->set_amount($amount);

        $update_args = array(
            'ID' => $payment_id
        );

        if (!empty($status)) $update_args['post_status'] = $status;
        if (!empty($date)) {
            $date .= ' 00:00:00';
            // $update_args['post_date'] = $date;
            $update_args['post_date_gmt'] = $date;
        }

        if (count($update_args)>1) {
            wp_update_post($update_args);
        }
    }
}