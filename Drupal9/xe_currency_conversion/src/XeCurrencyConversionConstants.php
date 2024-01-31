<?php

namespace Drupal\xe_currency_conversion;

/**
 * Defines constants for XE Currency Conversion module.
 */
final class XeCurrencyConversionConstants {

  /**
   * The base URL for the xe.com feed.
   */
  const BASE_URL = 'https://apilayer.net/api/';

  /**
   * The table name for this module.
   */
  const TABLE_NAME = 'xe_currency_conversion';

  /**
   * The time window constants for xe updates on cron run.
   */
  const WINDOW_OPEN = '23:45:00';
  const WINDOW_CLOSE = '00:15:00 + 1 day';

  /**
   * The max process time for queue operations.
   */
  const DEFAULT_MAX_TASK_TIME = 60;

}
