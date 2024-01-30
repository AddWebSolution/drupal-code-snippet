Installation
---------------------------------------------------------------------------------------------
Install the module as usual.
Go to the "XE Currency Conversion" configuration page in the Drupal admin interface.
Select the node types for which you want to enable xe currency conversion.
Click "Save configuration".
Go to the "XE Currency Conversion" menu in the Drupal admin interface.
Click "Import" to import the xe.com currency conversion feeds.
Click "Update" to update the xe.com currency conversion feeds.

Usage
---------------------------------------------------------------------------------------------
Go to the node view page for a node type that has xe currency conversion enabled.
The xe currency conversion price will be displayed next to the original price.
To convert the price to a different currency, use the "Convert" form provided by the xe currency conversion module.

Dependencies
---------------------------------------------------------------------------------------------
This module depends on the following Drupal modules:

Drupal 9/10.
Views module.
Apache Solr search module.

Configuration
---------------------------------------------------------------------------------------------
The module has the following configuration options:

xe_currency_conversion_base_url: The base URL for the xe.com feed.
xe_currency_conversion_table: The table name for this module.
xe_currency_conversion_window_open: The time window for xe updates on cron run.
xe_currency_conversion_window_close: The after midnight window close for xe updates on cron run.
xe_currency_conversion_default_max_task_time: The max process time for queue operations.

Troubleshooting
---------------------------------------------------------------------------------------------
If you encounter any issues with the module, you can try the following steps:

Check the Drupal logs for any error messages related to the module.
Check the xe.com API documentation for any changes or updates that may affect the module.

Maintainer
---------------------------------------------------------------------------------------------
This module is maintained by addwebsolution.
