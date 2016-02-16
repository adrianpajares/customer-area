<?php
/*  Copyright 2015 MarvinLabs (contact@marvinlabs.com) */

require_once(CUAR_INCLUDES_DIR . '/core-classes/Content/custom-post.class.php');

class CUAR_Payment extends CUAR_CustomPost
{
    public static $POST_TYPE = 'cuar_payment';

    public static $META_OBJECT_ID = 'cuar_object_id';
    public static $META_OBJECT_TYPE = 'cuar_object_type';
    public static $META_AMOUNT = 'cuar_amount';
    public static $META_CURRENCY = 'cuar_currency';
    public static $META_GATEWAY = 'cuar_gateway';
    public static $META_ADDRESS = 'cuar_address';
    public static $META_USER_ID = 'cuar_user_id';
    public static $META_USER_IP = 'cuar_user_ip';
    public static $META_NOTES = 'cuar_notes';

    /**
     * Constructor
     *
     * @param WP_Post|int $custom_post
     * @param boolean     $load_post If we supply an int as the first argument, shall we load the post object?
     */
    public function __construct($custom_post, $load_post = true)
    {
        parent::__construct($custom_post, $load_post);
    }

    /**
     * Update the status of the payment
     *
     * @param string $new_status
     */
    public function update_status($new_status)
    {
        if ($new_status == 'completed' || $new_status == 'complete') {
            $new_status = 'publish';
        }

        $old_status = $this->get_post()->post_status;

        if ($old_status === $new_status) {
            return; // Don't permit status changes that aren't changes
        }

        $do_change = apply_filters('cuar/core/payments/can-update-status', true, $this, $old_status, $new_status);

        if ($do_change) {
            do_action('cuar/core/payments/before-update-status', $this, $old_status, $new_status);

            $args = array(
                'ID'          => $this->ID,
                'post_status' => $new_status,
                'edit_date'   => current_time('mysql'),
            );
            wp_update_post(apply_filters('cuar/core/payments/update-payment-status-args', $args));

            do_action('cuar/core/payments/on-status-updated', $this->ID, $new_status, $old_status);
        }
    }



    //------- ACCESSORS -----------------------------------------------------------------------------------------------/

    /**
     * Set the object for which the payment was made
     *
     * @param string $object_type
     * @param int    $object_id
     */
    public function set_object($object_type, $object_id)
    {
        update_post_meta($this->ID, self::$META_OBJECT_ID, $object_id);
        update_post_meta($this->ID, self::$META_OBJECT_TYPE, $object_type);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return array ('id', 'type')
     */
    public function get_object()
    {
        $object_id = get_post_meta($this->ID, self::$META_OBJECT_ID, true);
        $object_type = get_post_meta($this->ID, self::$META_OBJECT_TYPE, true);

        return array(
            'type' => $object_type,
            'id'   => $object_id,
        );
    }

    /**
     * Set the object for which the payment was made
     *
     * @param string $gateway
     */
    public function set_gateway($gateway)
    {
        update_post_meta($this->ID, self::$META_GATEWAY, $gateway);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return string
     */
    public function get_gateway()
    {
        $gateway = get_post_meta($this->ID, self::$META_GATEWAY, true);

        return $gateway;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set_gateway_meta($key, $value)
    {
        update_post_meta($this->ID, 'cuar_gateway_' . $key, $value);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get_gateway_meta($key)
    {
        $value = get_post_meta($this->ID, 'cuar_gateway_' . $key, true);

        return $value;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param array $address
     */
    public function set_address($address)
    {
        $address = CUAR_AddressHelper::sanitize_address($address);
        update_post_meta($this->ID, self::$META_ADDRESS, $address);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return array
     */
    public function get_address()
    {
        $address = get_post_meta($this->ID, self::$META_ADDRESS, true);
        $address = CUAR_AddressHelper::sanitize_address($address);

        return $address;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        update_post_meta($this->ID, self::$META_USER_ID, $user_id);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return int
     */
    public function get_user_id()
    {
        $user_id = get_post_meta($this->ID, self::$META_USER_ID, true);

        return $user_id;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param string $user_ip
     */
    public function set_user_ip($user_ip)
    {
        update_post_meta($this->ID, self::$META_USER_IP, $user_ip);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return string
     */
    public function get_user_ip()
    {
        $user_ip = get_post_meta($this->ID, self::$META_USER_IP, true);

        return $user_ip;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param array $notes
     */
    public function set_notes($notes)
    {
        update_post_meta($this->ID, self::$META_NOTES, $notes);
    }

    /**
     * Get the object for which the payment was made
     *
     * @param string $note
     */
    public function add_note($note)
    {
        $notes = $this->get_notes();
        $notes[] = $note;
        $this->set_notes($notes);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return array
     */
    public function get_notes()
    {
        $notes = get_post_meta($this->ID, self::$META_NOTES, true);

        if (!isset($notes) || !is_array($notes)) $notes = array();

        return $notes;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param string $currency
     */
    public function set_currency($currency)
    {
        update_post_meta($this->ID, self::$META_CURRENCY, $currency);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return string
     */
    public function get_currency()
    {
        $currency = get_post_meta($this->ID, self::$META_CURRENCY, true);

        return $currency;
    }

    /**
     * Set the object for which the payment was made
     *
     * @param double $amount
     */
    public function set_amount($amount)
    {
        update_post_meta($this->ID, self::$META_AMOUNT, $amount);
    }

    /**
     * Get the object for which the payment was made
     *
     * @return double
     */
    public function get_amount()
    {
        $amount = get_post_meta($this->ID, self::$META_AMOUNT, true);

        return $amount;
    }

    //------- UTILITY FUNCTIONS ---------------------------------------------------------------------------------------/

    /**
     * Register the custom post type
     */
    public static function register_post_type()
    {
        $labels = array(
            'name'               => _x('Payments', 'cuar_payment', 'cuar'),
            'singular_name'      => _x('Payment', 'cuar_payment', 'cuar'),
            'add_new'            => _x('Add New', 'cuar_payment', 'cuar'),
            'add_new_item'       => _x('Add New Payment', 'cuar_payment', 'cuar'),
            'edit_item'          => _x('Edit Payment', 'cuar_payment', 'cuar'),
            'new_item'           => _x('New Payment', 'cuar_payment', 'cuar'),
            'view_item'          => _x('View Payment', 'cuar_payment', 'cuar'),
            'search_items'       => _x('Search Payments', 'cuar_payment', 'cuar'),
            'not_found'          => _x('No payment found', 'cuar_payment', 'cuar'),
            'not_found_in_trash' => _x('No payment found in Trash', 'cuar_payment', 'cuar'),
            'parent_item_colon'  => _x('Parent Payment:', 'cuar_payment', 'cuar'),
            'menu_name'          => _x('Payments', 'cuar_payment', 'cuar'),
        );

        $args = array(
            'labels'              => $labels,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'author'),
            'taxonomies'          => array(),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'publicly_queryable'  => true,
            'exclude_from_search' => true,
            'has_archive'         => false,
            'query_var'           => self::$POST_TYPE,
            'can_export'          => false,
            'rewrite'             => false,
            'capabilities'        => array(
                'edit_post'          => 'cuar_pay_edit',
                'edit_posts'         => 'cuar_pay_edit',
                'edit_others_posts'  => 'cuar_pay_edit',
                'publish_posts'      => 'cuar_pay_edit',
                'read_post'          => 'cuar_pay_read',
                'read_private_posts' => 'cuar_pay_list_all',
                'delete_post'        => 'cuar_pay_delete',
                'delete_posts'       => 'cuar_pay_delete',
            ),
        );

        register_post_type(self::$POST_TYPE, apply_filters('cuar/core/payments/register-post-type-args', $args));
    }

}