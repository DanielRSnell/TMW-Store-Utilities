# TMW Store Utilities

A collection of utilities for WooCommerce stores, designed to streamline store management and improve customer experience.

## Description

TMW Store Utilities is a modular WordPress plugin that provides various utilities for WooCommerce stores. The plugin is built with a modular architecture, making it easy to add new features while keeping existing functionality isolated and maintainable.

## Features

### Create and Link Customer Accounts
- Adds an order action to create WordPress user accounts from guest order billing information
- Only appears for orders without linked customer accounts (guest orders)
- Automatically links newly created accounts to the order
- Handles existing email addresses by linking to existing accounts instead of creating duplicates
- Copies all billing and shipping information to the user profile
- Sends new user notification emails with login credentials
- Provides admin feedback through success/error notices

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- WooCommerce 3.0 or higher
- HPOS (High-Performance Order Storage) compatible

## Installation

1. Download the plugin files
2. Upload the plugin folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Ensure WooCommerce is installed and activated

## Usage

### Creating Customer Accounts from Guest Orders

1. Navigate to **WooCommerce → Orders** in your WordPress admin
2. Open any guest order (orders without a linked customer account)
3. In the **Order Actions** dropdown, select "Create customer account from billing info"
4. Click **Update**
5. The plugin will:
   - Create a new WordPress user account using the billing information
   - Link the account to the order
   - Send login credentials to the customer via email
   - Display a success message

**Note:** If a user account with the same email already exists, the plugin will link the existing account to the order instead of creating a duplicate.

## Plugin Architecture

TMW Store Utilities uses a modular architecture for easy expansion:

```
TMW Store Utilities/
├── create-and-link-customer-account.php (main plugin file)
├── includes/
│   ├── class-admin-notices.php (shared admin notices)
│   └── class-module-loader.php (automatic module loader)
├── modules/
│   └── create-and-link/
│       └── class-create-and-link-customer.php
└── README.md
```

### Adding New Modules

To add a new utility module:

1. Create a new directory in `modules/`
2. Add your PHP class files to the directory
3. Update the `initialize_module()` method in `includes/class-module-loader.php`

Example:
```php
case 'my-new-utility':
    if (class_exists('TMW_My_New_Utility')) {
        new TMW_My_New_Utility();
    }
    break;
```

## HPOS Compatibility

This plugin is fully compatible with WooCommerce's High-Performance Order Storage (HPOS) and properly declares this compatibility to avoid warnings in WooCommerce admin.

## Support

For support, questions, or feature requests, please visit [Texas Metal Works](https://texasmetalowrks.com).

## Changelog

### Version 1.0.0
- Initial release
- Create and link customer accounts module
- HPOS compatibility
- Modular architecture implementation

## Author

**Texas Metal Works**
Website: [https://texasmetalowrks.com](https://texasmetalowrks.com)

## License

This plugin is proprietary software developed for Texas Metal Works.