# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TMW Store Utilities is a modular WordPress plugin that provides utilities for WooCommerce stores. The plugin uses a modular architecture where individual utilities are isolated in separate modules, making it easy to add new features without affecting existing functionality.

## Architecture

### Modular Plugin Structure
The plugin follows a modular architecture with these key components:

- **Main Plugin File** (`tmw-store-utilities.php`): Bootstrap file that declares HPOS compatibility and initializes the plugin
- **Module Loader** (`includes/class-module-loader.php`): Automatically discovers and loads all modules from the `/modules/` directory
- **Admin Notices** (`includes/class-admin-notices.php`): Shared notification system used across all modules
- **Modules Directory** (`modules/`): Each utility is contained in its own subdirectory

### Module System
The plugin automatically loads all modules by:
1. Scanning the `modules/` directory for subdirectories
2. Including all PHP files in each module directory
3. Initializing modules via the `initialize_module()` switch statement

### Current Modules
- **create-and-link**: Creates WordPress user accounts from WooCommerce guest order billing information

### Key Constants
- `TMW_PLUGIN_FILE`: Main plugin file path
- `TMW_PLUGIN_PATH`: Plugin directory path
- `TMW_PLUGIN_URL`: Plugin URL

### Naming Conventions
- All classes use `TMW_` prefix
- Text domain: `tmw-store-utilities`
- Transients use `tmw_` prefix
- WooCommerce hooks use standard WooCommerce naming

## Adding New Modules

To create a new utility module:

1. Create a new directory under `modules/` (e.g., `modules/my-new-utility/`)
2. Add PHP class files to the directory
3. Add initialization case to `includes/class-module-loader.php` in the `initialize_module()` method:

```php
case 'my-new-utility':
    if (class_exists('TMW_My_New_Utility')) {
        new TMW_My_New_Utility();
    }
    break;
```

4. Use `TMW_Admin_Notices::add_notice($message, $type)` for user feedback

## WooCommerce Integration

- Plugin declares HPOS compatibility via `declare_hpos_compatibility()` method
- Uses modern WooCommerce order APIs (`$order->get_*()`, `$order->set_*()`)
- Requires WooCommerce 3.0+ and is tested up to 8.0
- Integrates with WooCommerce order actions system

## WordPress Standards

- Follows WordPress coding standards and security practices
- Uses proper WordPress hooks and filters
- Includes proper capability checks and nonce verification where applicable
- Uses WordPress transients for temporary data storage (admin notices)