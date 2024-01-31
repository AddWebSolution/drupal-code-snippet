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
2. Configure the XE Currency Conversion settings
at `/admin/config/xe_currency_conversion/settings`.

## Usage

- Access the XE Currency Conversion settings page to configure API credentials.
- Run manual import or set up automated import.
- View imported currency rates in the Drupal database.
- Automatic cron job setup for periodic data updates.
- Use Drush commands for manual import.

## Drush Commands

drush xe-currency-import

## Views Integration

The module provides Views integration for the XE Currency Conversion table.

## Requirements

- Drupal 9 or later.

## Configuration

Configure the module by navigating to `/admin/config/xe_currency_conversion/settings`.

## Automatic Data Updates with Cron

The module supports automatic data updates using Drupal's cron system.

## Developers

- Maintainer: addwebsolution

## Issues

If you encounter any issues or have suggestions,
please [create an issue](https://github.com/AddWebSolution/drupal-code-snippet/issues) on GitHub.

## License

This module is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
