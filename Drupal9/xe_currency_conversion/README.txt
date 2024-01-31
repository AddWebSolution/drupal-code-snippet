# XE Currency Conversion Module

The XE Currency Conversion module provides integration with XE.com 
to import and manage currency conversion rates.

## Features

- Fetches live currency rates from XE.com API.
- Allows configuration of XE.com API credentials.
- Obtain your XE.com API credentials by signing up at [https://apilayer.com/].
- Imports and stores currency conversion rates in the Drupal database.
- Drush commands for manual import.

## Installation

1. Install and enable the module on your Drupal site.
2. Configure the XE Currency Conversion settings at `/admin/config/xe_currency_conversion/settings`.

## Usage

- Access the XE Currency Conversion settings page to configure API credentials.
- Run manual import or set up automated import through batch processing.
- View imported currency rates in the Drupal database.
- Automatic cron job setup for periodic data updates.
- Use Drush commands for manual import.

## Drush Commands

lando drush xe-currency-import

## Views Integration

The module provides Views integration for the XE Currency Conversion table.

## Requirements

- Drupal 9 or later.

## Configuration

Configure the module by navigating to `/admin/config/xe_currency_conversion/settings`.

## Automatic Data Updates with Cron

The module supports automatic data updates using Drupal's cron system.
To set up automatic updates, follow these steps:

1. **Define Base URL**: Define the base URL for the XE.com feed as a constant.
     Add the following line to your settings file or module file:
    define('XE_CURRENCY_CONVERSION_BASE_URL', 'https://apilayer.net/api/');
2. **Define Table Name**: Define the table name for this module.
     Add the following line to your settings file or module file:
    define('XE_CURRENCY_CONVERSION_TABLE', 'xe_currency_conversion');
3. **Define Time Windows**: Define the time windows for XE updates on cron run.
     Add the following lines to your settings file or module file:
    // The time window constants for XE updates on cron run.
    define('XE_CURRENCY_CONVERSION_WINDOW_OPEN', '23:45:00');
    define('XE_CURRENCY_CONVERSION_WINDOW_CLOSE', '00:15:00 + 1 day');
4. **Set Max Process Time**: Set the maximum process time for queue operations.
    Add the following line to your settings file or module file:
    define('XE_CURRENCY_CONVERSION_DEFAULT_MAX_TASK_TIME', 60);
5. **Ensure Cron Runs Regularly**: Ensure that Drupal's cron runs
     regularly by setting up a cron job at the server level. You can do
     this by adding the following line to your server's crontab:
    */15 * * * * cd /path/to/drupal && /usr/bin/php /usr/bin/drush cron:execute --root=/path/to/drupal > /dev/null 2>&1
    Adjust the paths according to your Drupal installation.

## Developers

- Maintainer: addwebsolution

## Issues

If you encounter any issues or have suggestions,
please [create an issue](https://github.com/AddWebSolution/drupal-code-snippet/issues) on GitHub.

## License

This module is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

