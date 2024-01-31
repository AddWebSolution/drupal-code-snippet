<?php

namespace Drupal\xe_currency_conversion\Commands;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\xe_currency_conversion\XeCurrencyConversionService;
use Drush\Commands\DrushCommands;

/**
 * Drush service for XE Currency Conversion module.
 */
class XeCurrencyConversionDrushService extends DrushCommands {

  /**
   * The XE Currency Conversion service.
   *
   * @var \Drupal\xe_currency_conversion\XeCurrencyConversionService
   */
  protected $xeCurrencyConversionService;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new XeCurrencyConversionDrushService object.
   *
   * @param \Drupal\xe_currency_conversion\XeCurrencyConversionService $xeCurrencyConversionService
   *   The XE Currency Conversion service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(XeCurrencyConversionService $xeCurrencyConversionService, MessengerInterface $messenger) {
    $this->xeCurrencyConversionService = $xeCurrencyConversionService;
    $this->messenger = $messenger;
  }

  /**
   * Drush command callback for importing XE Currency Conversion data.
   *
   * @command xe-currency-import
   * @aliases xeci
   * @usage drush xe-currency-import
   *   Imports XE Currency Conversion data.
   */
  public function importData() {
    $this->output()->writeln('Importing currency conversion data...');

    // Call the importData method from the service.
    $result = $this->xeCurrencyConversionService->importData();

    // Check if the service provided any data.
    if ($result) {
      $this->output()->writeln('Data imported successfully.');
    }
    else {
      $this->output()->writeln('No data imported. Check if the Client API ID is empty or if there was an issue with the service.');
    }
  }

}
