<?php

if (!defined('ABSPATH')) {
    exit;
}

class TMW_Module_Loader {

    private static $modules = array();

    public static function init() {
        self::load_modules();
    }

    private static function load_modules() {
        $modules_dir = TMW_PLUGIN_PATH . 'modules/';

        if (!is_dir($modules_dir)) {
            return;
        }

        $module_directories = glob($modules_dir . '*', GLOB_ONLYDIR);

        foreach ($module_directories as $module_dir) {
            $module_name = basename($module_dir);
            self::load_module($module_name, $module_dir);
        }
    }

    private static function load_module($module_name, $module_path) {
        $module_files = glob($module_path . '/*.php');

        if (empty($module_files)) {
            return;
        }

        foreach ($module_files as $file) {
            if (is_readable($file)) {
                require_once $file;
            }
        }

        self::initialize_module($module_name);
        self::$modules[] = $module_name;
    }

    private static function initialize_module($module_name) {
        switch ($module_name) {
            case 'create-and-link':
                if (class_exists('TMW_Create_And_Link_Customer')) {
                    new TMW_Create_And_Link_Customer();
                }
                break;
        }
    }

    public static function get_loaded_modules() {
        return self::$modules;
    }

    public static function is_module_loaded($module_name) {
        return in_array($module_name, self::$modules);
    }
}