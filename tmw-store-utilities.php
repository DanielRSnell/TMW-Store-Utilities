<?php
/**
 * Plugin Name: TMW Store Utilities
 * Description: A collection of utilities for WooCommerce stores including customer account creation and linking for guest orders.
 * Version: 1.0.0
 * Author: Texas Metal Works
 * Author URI: https://texasmetalowrks.com
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 * Text Domain: tmw-store-utilities
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TMW_PLUGIN_FILE', __FILE__);
define('TMW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TMW_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!class_exists('TMW_Store_Utilities')) {

    class TMW_Store_Utilities {

        public function __construct() {
            add_action('plugins_loaded', array($this, 'init'));
            add_action('before_woocommerce_init', array($this, 'declare_hpos_compatibility'));
        }

        public function init() {
            if (!class_exists('WooCommerce')) {
                add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
                return;
            }

            $this->load_includes();
            $this->init_modules();
        }

        private function load_includes() {
            require_once TMW_PLUGIN_PATH . 'includes/class-admin-notices.php';
            require_once TMW_PLUGIN_PATH . 'includes/class-module-loader.php';

            TMW_Admin_Notices::init();
        }

        private function init_modules() {
            TMW_Module_Loader::init();
        }

        public function declare_hpos_compatibility() {
            if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            }
        }

        public function woocommerce_missing_notice() {
            echo '<div class="error"><p><strong>TMW Store Utilities</strong> requires WooCommerce to be installed and active.</p></div>';
        }
    }

    new TMW_Store_Utilities();
}