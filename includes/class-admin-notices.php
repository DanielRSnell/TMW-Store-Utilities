<?php

if (!defined('ABSPATH')) {
    exit;
}

class TMW_Admin_Notices {

    public static function init() {
        add_action('admin_notices', array(__CLASS__, 'display_admin_notices'));
    }

    public static function add_notice($message, $type = 'info') {
        $notices = get_transient('tmw_admin_notices') ?: array();
        $notices[] = array(
            'message' => $message,
            'type' => $type
        );
        set_transient('tmw_admin_notices', $notices, 60);
    }

    public static function display_admin_notices() {
        $notices = get_transient('tmw_admin_notices');
        if (!empty($notices)) {
            foreach ($notices as $notice) {
                $class = 'notice notice-' . $notice['type'] . ' is-dismissible';
                printf('<div class="%s"><p>%s</p></div>', esc_attr($class), esc_html($notice['message']));
            }
            delete_transient('tmw_admin_notices');
        }
    }
}