<?php

if (!defined('ABSPATH')) {
    exit;
}

class TMW_Create_And_Link_Customer {

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        add_filter('woocommerce_order_actions', array($this, 'add_order_action'));
        add_action('woocommerce_order_action_create_customer_account', array($this, 'process_create_customer_account'));
    }

    public function add_order_action($actions) {
        global $theorder;

        if (!$theorder || $theorder->get_user_id() > 0) {
            return $actions;
        }

        $actions['create_customer_account'] = __('Create customer account from billing info', 'tmw-store-utilities');

        return $actions;
    }

    public function process_create_customer_account($order) {
        if (!$order || $order->get_user_id() > 0) {
            TMW_Admin_Notices::add_notice('This order already has a customer account linked.', 'error');
            return;
        }

        $billing_email = $order->get_billing_email();

        if (empty($billing_email)) {
            TMW_Admin_Notices::add_notice('No billing email found for this order.', 'error');
            return;
        }

        if (email_exists($billing_email)) {
            $existing_user = get_user_by('email', $billing_email);
            if ($existing_user) {
                $order->set_customer_id($existing_user->ID);
                $order->save();
                TMW_Admin_Notices::add_notice('Customer account already exists. Order has been linked to existing account.', 'success');
                return;
            }
        }

        $user_id = $this->create_user_from_billing($order);

        if (is_wp_error($user_id)) {
            TMW_Admin_Notices::add_notice('Failed to create customer account: ' . $user_id->get_error_message(), 'error');
            return;
        }

        $order->set_customer_id($user_id);
        $order->save();

        TMW_Admin_Notices::add_notice('Customer account created and linked successfully!', 'success');
    }

    private function create_user_from_billing($order) {
        $billing_email = $order->get_billing_email();
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();

        $username = $this->generate_username($billing_email, $billing_first_name, $billing_last_name);
        $password = wp_generate_password();

        $user_data = array(
            'user_login' => $username,
            'user_email' => $billing_email,
            'user_pass' => $password,
            'first_name' => $billing_first_name,
            'last_name' => $billing_last_name,
            'display_name' => trim($billing_first_name . ' ' . $billing_last_name),
            'role' => 'customer'
        );

        $user_id = wp_insert_user($user_data);

        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, 'billing_first_name', $billing_first_name);
            update_user_meta($user_id, 'billing_last_name', $billing_last_name);
            update_user_meta($user_id, 'billing_email', $billing_email);
            update_user_meta($user_id, 'billing_phone', $order->get_billing_phone());
            update_user_meta($user_id, 'billing_company', $order->get_billing_company());
            update_user_meta($user_id, 'billing_address_1', $order->get_billing_address_1());
            update_user_meta($user_id, 'billing_address_2', $order->get_billing_address_2());
            update_user_meta($user_id, 'billing_city', $order->get_billing_city());
            update_user_meta($user_id, 'billing_state', $order->get_billing_state());
            update_user_meta($user_id, 'billing_postcode', $order->get_billing_postcode());
            update_user_meta($user_id, 'billing_country', $order->get_billing_country());

            if ($order->get_shipping_first_name()) {
                update_user_meta($user_id, 'shipping_first_name', $order->get_shipping_first_name());
                update_user_meta($user_id, 'shipping_last_name', $order->get_shipping_last_name());
                update_user_meta($user_id, 'shipping_company', $order->get_shipping_company());
                update_user_meta($user_id, 'shipping_address_1', $order->get_shipping_address_1());
                update_user_meta($user_id, 'shipping_address_2', $order->get_shipping_address_2());
                update_user_meta($user_id, 'shipping_city', $order->get_shipping_city());
                update_user_meta($user_id, 'shipping_state', $order->get_shipping_state());
                update_user_meta($user_id, 'shipping_postcode', $order->get_shipping_postcode());
                update_user_meta($user_id, 'shipping_country', $order->get_shipping_country());
            }

            wp_new_user_notification($user_id, null, 'user');
        }

        return $user_id;
    }

    private function generate_username($email, $first_name = '', $last_name = '') {
        $username = '';

        if (!empty($first_name) && !empty($last_name)) {
            $username = sanitize_user(strtolower($first_name . '.' . $last_name), true);
        }

        if (empty($username) || username_exists($username)) {
            $username = sanitize_user(strtolower(explode('@', $email)[0]), true);
        }

        if (username_exists($username)) {
            $i = 1;
            $original_username = $username;
            while (username_exists($username)) {
                $username = $original_username . $i;
                $i++;
            }
        }

        return $username;
    }
}